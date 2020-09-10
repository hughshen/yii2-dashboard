;window.MediaManager = {
    currentPage: 1,
    currentPath: '',
    dataLoaded: false,
    modalLoaded: false,
    eventsLoaded: false,
    $wrap: null,
    $toggle: null,

    options: {},
    defaults: {
        id: 'media-manager',
        title: 'Media Manager',
        mediaUrl: '/media/list',
    },

    init: function () {
        this.options = this.defaults;
        if (arguments[0] && typeof arguments[0] === 'object') {
            this.options = this.bindOptions(this.defaults, arguments[0]);
        }

        this.initModal();
        this.initWrapEvents();
    },

    initWrapEvents: function () {
        self = this;

        if (!self.modalLoaded || self.eventsLoaded) return;

        self.$wrap = $('#' + self.options.id + '-wrap');

        self.$wrap.on('dblclick', '.media-file .media-thumbnail', function () {
            let linkEle = $(this).find('.media-link').eq(0);
            if (!linkEle) return;

            self.doToggle(linkEle.attr('data-url'), linkEle.attr('data-image'));
        });

        self.$wrap.on('click', '.media-dir .media-thumbnail', function () {
            self.currentPath = $(this).find('.media-link').eq(0).attr('data-path');
            self.dataReload(true);
        });

        self.$wrap.on('click', '.breadcrumb .media-link', function () {
            self.currentPath = $(this).attr('data-path');
            self.dataReload(true);
        });

        self.$wrap.on('click', '.pagination a', function (e) {
            e.preventDefault();
            let page = $(this).attr('data-page');
            page = typeof page === 'undefined' ? 0 : parseInt(page);

            self.currentPage = page + 1;
            self.dataReload(true);
        });
    },

    setToggle: function (toggleId, toggleModal) {
        this.$toggle = $(toggleId).eq(0);
        if (toggleModal) {
            this.toggleModal();
        }
    },

    doToggle: function (url, imageFlag) {
        if (!this.$toggle) return;

        let $input = $(this.$toggle.attr('data-input')).eq(0);
        if ($input) {
            $input.val(url);
        }

        let $preview = $(this.$toggle.attr('data-preview')).eq(0);
        if ($preview) {
            if (imageFlag === '0') {
                $preview.css('display', 'none');
            } else {
                $preview.css('display', '');
                if ($preview.prop('tagName') === 'IMG') {
                    $preview.attr('src', url);
                } else {
                    $preview.find('img').eq(0).attr('src', url);
                }
            }
        }

        this.hideModal();
    },


    dataReload: function (forceLoad) {
        self = this;
        if (!self.datalLoaded || forceLoad) {
            $.get(self.options.mediaUrl, {path: self.currentPath, page: self.currentPage}, function (data) {
                self.datalLoaded = true;
                self.$wrap.html(data);
            });
        }
    },

    initModal: function () {
        this.modalLoaded = true;
        if ($('#' + this.options.id).length) return;

        $('body').append('<div id="' + this.options.id + '" class="fade modal" role="dialog" tabindex="-1" style="display: none;">'
            + '<div class="modal-dialog">'
            + '<div class="modal-content">'
            + '<div class="modal-header">'
            + '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>'
            + '<h2>' + this.options.title + '</h2>'
            + '</div>'
            + '<div class="modal-body">'
            + '<div class="media-wrap" id="' + this.options.id + '-wrap"></div>'
            + '</div>'
            + '</div>'
            + '</div>'
            + '</div>');
    },

    showModal: function () {
        $('#' + this.options.id).modal('show');
    },

    hideModal: function () {
        $('#' + this.options.id).modal('hide');
    },

    toggleModal: function () {
        this.showModal();
        this.dataReload();
    },

    bindOptions: function (options, properties) {
        let property;
        for (property in properties) {
            if (properties.hasOwnProperty(property)) {
                options[property] = properties[property];
            }
        }
        return options;
    }
};
