;(function(angular) {
    "use strict";
    angular.module('df.app',['df.common']);
    angular.module('df.common',['angularFileUpload','ui.select2']);
    angular.module('df.admin',['df.app']);
    angular.module('df.crm',['df.app']);
}(angular));

