(function () {
    this.initMediaManager = function () {

        // Defaults options
        var options = {
            id: 'media-unique-id',
            target: '.media-manager-target',
            targetView: '.media-target-view',
            toggle: '.media-manager-toggle',
            managerUrl: 'index.php?r=/media/manager/popup',
        };

        if (arguments[0] && typeof arguments[0] === 'object') {
            this.options = bindOptions(options, arguments[0]);
        }

        if (!options.target || !options.toggle) return;

        appendModal();

        var wrap = $('#' + options.id + '-media-wrap');
        var target = $(options.target);
        var toggle = $(options.toggle);
        var targetView = $(options.targetView);
        var datalLoaded = false;

        var currentPage = 1;
        var currentPath = '';

        toggle.on('click', function () {
            showModal();
            if (!datalLoaded) {
                $.get(options.managerUrl, function (data) {
                    datalLoaded = true;
                    wrap.html(data);
                });
            }
        });
        wrap.on('dblclick', '.media-file .media-thumbnail', function () {
            var linkEle = $(this).find('.media-link').eq(0);
            target.val(linkEle.attr('data-url'));
            if (targetView.length > 0) {
                if (linkEle.attr('data-image') === '0') {
                    targetView.css('display', 'none');
                } else {
                    targetView.css('display', '');
                    targetView.find('img').eq(0).attr('src', linkEle.attr('data-url'));
                }
            }
            hideModal();
        });
        wrap.on('click', '.media-dir .media-thumbnail', function () {
            currentPath = $(this).find('.media-link').eq(0).attr('data-path');
            $.get(options.managerUrl, {path: currentPath}, function (data) {
                wrap.html(data);
            });
        });
        wrap.on('click', '.breadcrumb .media-link', function () {
            currentPath = $(this).attr('data-path');
            $.get(options.managerUrl, {path: currentPath}, function (data) {
                wrap.html(data);
            });
        });
        wrap.on('click', '.pagination a', function (e) {
            e.preventDefault();
            var page = $(this).attr('data-page');
            page = typeof page === 'undefined' ? 0 : parseInt(page);
            currentPage = page + 1;
            $.get(options.managerUrl, {page: currentPage, path: currentPath}, function (data) {
                wrap.html(data);
            });
        });

        function appendModal() {
            if ($('#' + options.id).length) return;

            $('body').append('<div id="' + options.id + '" class="fade modal" role="dialog" tabindex="-1" style="display: none;">'
                + '<div class="modal-dialog">'
                + '<div class="modal-content">'
                + '<div class="modal-header">'
                + '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>'
                + '<h2>Media Manager</h2>'
                + '</div>'
                + '<div class="modal-body">'
                + '<div class="media-wrap" id="' + options.id + '-media-wrap"></div>'
                + '</div>'
                + '</div>'
                + '</div>'
                + '</div>');
        }

        function showModal() {
            $('#' + options.id).modal('show');
        }

        function hideModal() {
            $('#' + options.id).modal('hide');
        }
    };

    function bindOptions(options, properties) {
        var property;
        for (property in properties) {
            if (properties.hasOwnProperty(property)) {
                options[property] = properties[property];
            }
        }
        return options;
    }
}());
