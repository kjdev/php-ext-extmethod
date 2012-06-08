#ifndef PHP_EXTMETHOD_H
#define PHP_EXTMETHOD_H

#define EXTMETHOD_EXT_VERSION "0.0.1"

extern zend_module_entry extmethod_module_entry;
#define phpext_extmethod_module_ptr &extmethod_module_entry

#ifdef PHP_WIN32
#   define PHP_EXTMETHOD_MODULE_API __declspec(dllexport)
#elif defined(__GNUC__) && __GNUC__ >= 4
#   define PHP_EXTMETHOD_MODULE_API __attribute__ ((visibility("default")))
#else
#   define PHP_EXTMETHOD_MODULE_API
#endif

#ifdef ZTS
#include "TSRM.h"
#endif

#endif  /* PHP_EXTMETHOD_H */
