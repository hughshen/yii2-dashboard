(function() {
    this.initMediaManager = function() {

        // Defaults options
        var options = {
            target: '.media-manager-target',
            toggle: '.media-manager-toggle',
            managerUrl: 'index.php?r=media/manager-list',
        };

        if (arguments[0] && typeof arguments[0] === 'object') {
            this.options = bindOptions(options, arguments[0]);
        }

        if (!options.target || !options.toggle) return;

        appendModal();

        var wrap = $('#manager-wrap');
        var target = $(options.target);
        var toggle = $(options.toggle);

        var currentPage = 1;
        var currentFolder = '';

        toggle.on('click', function() {
            showModal();
            $.get(options.managerUrl, function(data) {
                wrap.html(data);
            });
        });
        wrap.on('dblclick', '.item img', function() {
            target.val($(this).attr('data-url'));
            hideModal();
        });
        wrap.on('click', 'a', function(e) {
            e.preventDefault();
            var page = $(this).attr('data-page');
            page = parseInt(page);
            currentPage = page + 1;
            $.get(options.managerUrl, {page: currentPage, folder: currentFolder}, function(data) {
                wrap.html(data);
            });
        });
        wrap.on('click', '.folder-icon', function() {
            currentFolder = $(this).attr('data-folder');
            $.get(options.managerUrl, {folder: currentFolder}, function(data) {
                wrap.html(data);
            });
        });

        function appendModal() {
            var ele = $('#mdia-manager-modal');
            if (ele.length) return;

            $('body').append('<div id="mdia-manager-modal" class="fade modal" role="dialog" tabindex="-1" style="display: none;">'
                + '<div class="modal-dialog">'
                    + '<div class="modal-content">'
                        + '<div class="modal-header">'
                            + '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>'
                            + '<h2>Media Manager</h2>'
                        + '</div>'
                        + '<div class="modal-body">'
                            + '<div class="manager-wrap" id="manager-wrap"></div>'
                        + '</div>'
                    + '</div>'
                + '</div>'
            + '</div>');
        }

        function showModal() {
            $('#mdia-manager-modal').modal('show');
        }

        function hideModal() {
            $('#mdia-manager-modal').modal('hide');
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

