
#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "php_ini.h"
#include "ext/standard/info.h"
#include "ext/standard/php_smart_str.h"
#include "Zend/zend_closures.h"

#include "php_extmethod.h"

static HashTable *closures;

typedef struct {
    char *method;
    zval *closure;
    zend_function *fe;
} extmethod_data_t;

static inline extmethod_data_t *_extmethod_data_init(zval *function)
{
    extmethod_data_t *data =
        (extmethod_data_t *)emalloc(sizeof(extmethod_data_t));
    MAKE_STD_ZVAL(data->closure);
    data->fe = (zend_function *)emalloc(sizeof(zend_function));
    return data;
}

static inline void _extmethod_data_destroy(extmethod_data_t **data)
{
    if (*data) {
        if ((*data)->fe) {
            efree((*data)->fe);
        }
        if ((*data)->closure) {
            zval_ptr_dtor(&((*data)->closure));
        }
        if ((*data)->method) {
            efree((*data)->method);
        }
        efree(*data);
    }
}

ZEND_BEGIN_ARG_INFO_EX(arginfo_extmethod_intern, 0, 0, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_extmethod_factory, 0, 0, 3)
    ZEND_ARG_INFO(0, class)
    ZEND_ARG_INFO(0, method)
    ZEND_ARG_INFO(0, function)
    ZEND_ARG_INFO(0, flags)
ZEND_END_ARG_INFO()

ZEND_FUNCTION(extmethod_intern);
ZEND_FUNCTION(extmethod_factory);

const zend_function_entry extmethod_functions[] = {
    ZEND_FE(extmethod_factory, arginfo_extmethod_factory)
    { NULL, NULL, NULL, 0, 0 }
};

ZEND_MINIT_FUNCTION(extmethod)
{
    REGISTER_LONG_CONSTANT("EXTMETHOD_STATIC", ZEND_ACC_STATIC,
                           CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT("EXTMETHOD_PUBLIC", ZEND_ACC_PUBLIC,
                           CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT("EXTMETHOD_PROTECTED", ZEND_ACC_PROTECTED,
                           CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT("EXTMETHOD_PRIVATE", ZEND_ACC_PRIVATE,
                           CONST_CS | CONST_PERSISTENT);

    return SUCCESS;
}

ZEND_MSHUTDOWN_FUNCTION(extmethod)
{
    return SUCCESS;
}

ZEND_RINIT_FUNCTION(extmethod)
{
    ALLOC_HASHTABLE(closures);
    zend_hash_init(closures, 10, NULL,
                   (void (*)(void *))_extmethod_data_destroy, 0);
    return SUCCESS;
}

ZEND_RSHUTDOWN_FUNCTION(extmethod)
{
    if (closures) {
        zend_hash_destroy(closures);
        FREE_HASHTABLE(closures);
    }
    return SUCCESS;
}

ZEND_MINFO_FUNCTION(extmethod)
{
    php_info_print_table_start();
    php_info_print_table_header(2, "extmethod support", "enabled");
    php_info_print_table_header(2, "extension version", EXTMETHOD_EXT_VERSION);
    php_info_print_table_end();
}

zend_module_entry extmethod_module_entry = {
#if ZEND_MODULE_API_NO >= 20010901
    STANDARD_MODULE_HEADER,
#endif
    "extmethod",
    extmethod_functions,
    ZEND_MINIT(extmethod),
    ZEND_MSHUTDOWN(extmethod),
    ZEND_RINIT(extmethod),
    ZEND_RSHUTDOWN(extmethod),
    ZEND_MINFO(extmethod),
#if ZEND_MODULE_API_NO >= 20010901
    EXTMETHOD_EXT_VERSION,
#endif
    STANDARD_MODULE_PROPERTIES
};

#ifdef COMPILE_DL_EXTMETHOD
ZEND_GET_MODULE(extmethod)
#endif

#if ZEND_MODULE_API_NO < 20100525
typedef struct _zend_closure {
    zend_object    std;
    zend_function  func;
    HashTable     *debug_info;
} zend_closure;
#endif

ZEND_FUNCTION(extmethod_intern)
{
    zval *self = getThis();
    zend_class_entry *ce = NULL;
    const char *active = NULL;
    zval ***argv = NULL;
    int argc = 0;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
                              "*", &argv, &argc) == FAILURE) {
        return;
    }

    if (self && Z_OBJ_HT_P(self)->get_class_entry != NULL) {
        ce = Z_OBJCE_P(self);
    } else if (EG(called_scope)) {
        ce = EG(called_scope);
    } else if (EG(scope))  {
        ce = EG(scope);
    } else {
        zend_error(E_WARNING, "class entry not found");
        RETURN_FALSE;
    }

    active = get_active_function_name(TSRMLS_C);

    if (closures && active && ce->name) {
        smart_str key = {0};

        smart_str_appendl(&key, ce->name, ce->name_length);
        smart_str_appendl(&key, active, strlen(active));
        smart_str_0(&key);

        extmethod_data_t **data;
        if (zend_hash_find(closures, key.c, key.len,
                           (void **)&data) == SUCCESS) {
            zval *func, *retval_ptr;
            zval callback;
            zend_function *closure =
                (zend_function *)zend_get_closure_method_def((*data)->closure
                                                             TSRMLS_CC);
            MAKE_STD_ZVAL(func);
#if ZEND_MODULE_API_NO >= 20100525
            zend_create_closure(func, closure, ce, self TSRMLS_CC);
#else
            zend_create_closure(func, closure TSRMLS_CC);
            zend_closure *closure_obj =
                (zend_closure *)zend_object_store_get_object(func TSRMLS_CC);
            closure_obj->func.common.scope = ce;
#endif

            ZVAL_STRINGL(&callback, "__invoke", sizeof("__invoke") - 1, 0);

            if (call_user_function_ex(EG(function_table), &func,
                                      &callback, &retval_ptr, argc, argv,
                                      0, NULL TSRMLS_CC) == SUCCESS) {
                if (retval_ptr) {
                    COPY_PZVAL_TO_ZVAL(*return_value, retval_ptr);
                }
            } else {
                zend_error(E_WARNING, "failed to call the '%s::%s()'",
                           ce->name, active);
                RETVAL_FALSE;
            }
            zval_ptr_dtor(&func);
            smart_str_free(&key);
            return;
        }
        smart_str_free(&key);
    }
    zend_error(E_WARNING, "extend method not found");
    RETURN_FALSE;
}

ZEND_FUNCTION(extmethod_factory)
{
    zval *class, *function, *flags = NULL;
    char *method, *lcname;
    int method_len;
    zend_class_entry *ce = NULL, **pce;
    zend_bool method_exsits = 0;
    zend_uint fn_flags = 0;
    smart_str key = {0};

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC,
                              "zsz|z", &class, &method, &method_len,
                              &function, &flags) == FAILURE) {
        RETURN_FALSE;
    }

    /* Find class */
    if (Z_TYPE_P(class) == IS_OBJECT) {
        if (Z_OBJ_HT_P(class)->get_class_entry == NULL) {
            RETURN_FALSE;
        }
        ce = Z_OBJCE_P(class);
    } else if (Z_TYPE_P(class) == IS_STRING) {
        if (zend_lookup_class(Z_STRVAL_P(class), Z_STRLEN_P(class),
                              &pce TSRMLS_CC) == SUCCESS) {
            ce = *pce;
        }
    }

    if (!ce || !ce->name) {
        zend_error(E_WARNING, "class entry not found.");
        RETURN_FALSE;
    }

    /* Exsists class method */
    lcname = zend_str_tolower_dup(method, method_len);
    if (zend_hash_exists(&ce->function_table, lcname, method_len + 1)) {
        method_exsits = 1;
    } else {
        union _zend_function *func = NULL;
        if (Z_TYPE_P(class) == IS_OBJECT
            && Z_OBJ_HT_P(class)->get_method != NULL
#if ZEND_MODULE_API_NO >= 20100525
            && (func = Z_OBJ_HT_P(class)->get_method(&class, method, method_len,
                                                     NULL TSRMLS_CC)) != NULL
#else
            && (func = Z_OBJ_HT_P(class)->get_method(&class, method, method_len
                                                     TSRMLS_CC)) != NULL
#endif
            ) {
            if (func->type == ZEND_INTERNAL_FUNCTION
                && (func->common.fn_flags & ZEND_ACC_CALL_VIA_HANDLER) != 0) {
                if (func->common.scope == zend_ce_closure
                    && (method_len == sizeof(ZEND_INVOKE_FUNC_NAME) - 1)
                    && memcmp(lcname, ZEND_INVOKE_FUNC_NAME,
                              sizeof(ZEND_INVOKE_FUNC_NAME) - 1) == 0) {
                    method_exsits = 1;
                }
                efree((char*)((zend_internal_function*)func)->function_name);
            } else {
                method_exsits = 1;
            }
        }

        if (func) {
            efree(func);
        }
    }
    if (lcname) {
        efree(lcname);
    }

    if (method_exsits) {
        zend_error(E_WARNING, "%s::%s() is exsits", ce->name, method);
        RETURN_FALSE;
    }

    /* function */
    if (Z_TYPE_P(function) != IS_OBJECT ||
        !instanceof_function(Z_OBJCE_P(function), zend_ce_closure TSRMLS_CC)) {
        zend_error(E_WARNING, "function does not closure object");
        RETURN_FALSE;
    }

    /* flags */
    if (flags) {
        if (Z_LVAL_P(flags) & ZEND_ACC_STATIC) {
            fn_flags = ZEND_ACC_STATIC;
        }
        if (Z_LVAL_P(flags) & ZEND_ACC_PROTECTED) {
            fn_flags |= ZEND_ACC_PROTECTED;
        } else if (Z_LVAL_P(flags) & ZEND_ACC_PRIVATE) {
            fn_flags |= ZEND_ACC_PRIVATE;
        } else {
            fn_flags |= ZEND_ACC_PUBLIC;
        }
    } else {
        fn_flags = ZEND_ACC_PUBLIC;
    }

    //closure key: class + method
    smart_str_appendl(&key, ce->name, ce->name_length);
    smart_str_appendl(&key, method, method_len);
    smart_str_0(&key);

    //method
    extmethod_data_t *data = _extmethod_data_init(function);
    data->method = estrndup(method, method_len);

    //copy closure
    *(data->closure) = *function;
    zval_copy_ctor(data->closure);
    Z_SET_REFCOUNT_P(data->closure, 1);

    //method in extmethod_intern
    data->fe->common.function_name = data->method;
    data->fe->common.scope = ce;
    data->fe->common.fn_flags = fn_flags;
    data->fe->common.prototype = NULL;
    data->fe->common.num_args = 0;
    data->fe->common.required_num_args = 0;
    data->fe->common.arg_info = NULL;
    data->fe->type = ZEND_INTERNAL_FUNCTION;
    data->fe->internal_function.handler = ZEND_FN(extmethod_intern);
    data->fe->internal_function.module = 0;
    data->fe->internal_function.scope = ce;
    data->fe->internal_function.function_name = data->method;
    data->fe->internal_function.fn_flags = fn_flags;

    //add closures
    zend_hash_add(closures, key.c, key.len,
                  (void *)&data, sizeof(extmethod_data_t*), NULL);

    smart_str_free(&key);

    if (zend_hash_update(&ce->function_table, method, method_len + 1,
                         data->fe, sizeof(zend_function), NULL) != SUCCESS) {
        RETURN_FALSE;
    }

    RETURN_TRUE;
}
