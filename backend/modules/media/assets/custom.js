(function () {
    this.initMediaManager = function () {

        // Defaults options
        var options = {
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

        var wrap = $('#media-wrap');
        var target = $(options.target);
        var toggle = $(options.toggle);
        var targetView = $(options.targetView);

        var currentPage = 1;
        var currentFolder = '';

        toggle.on('click', function () {
            showModal();
            $.get(options.managerUrl, function (data) {
                wrap.html(data);
            });
        });
        wrap.on('dblclick', '.media-image .media-thumbnail', function () {
            target.val($(this).attr('data-path'));
            if (targetView.length > 0) {
                targetView.css('display', '');
                targetView.find('img').eq(0).attr('src', $(this).attr('data-path'));
            }
            hideModal();
        });
        wrap.on('click', 'a', function (e) {
            e.preventDefault();
            var page = $(this).attr('data-page');
            page = typeof page === 'undefined' ? 0 : parseInt(page);
            currentPage = page + 1;
            $.get(options.managerUrl, {page: currentPage, folder: currentFolder}, function (data) {
                wrap.html(data);
            });
        });
        wrap.on('click', '.media-folder .media-thumbnail', function () {
            currentFolder = $(this).attr('data-path');
            $.get(options.managerUrl, {folder: currentFolder}, function (data) {
                wrap.html(data);
            });
        });

        function appendModal() {
            var ele = $('#media-modal');
            if (ele.length) return;

            $('body').append('<div id="media-modal" class="fade modal" role="dialog" tabindex="-1" style="display: none;">'
                + '<div class="modal-dialog">'
                + '<div class="modal-content">'
                + '<div class="modal-header">'
                + '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>'
                + '<h2>Media Manager</h2>'
                + '</div>'
                + '<div class="modal-body">'
                + '<div class="media-wrap" id="media-wrap"></div>'
                + '</div>'
                + '</div>'
                + '</div>'
                + '</div>');
        }

        function showModal() {
            $('#media-modal').modal('show');
        }

        function hideModal() {
            $('#media-modal').modal('hide');
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
