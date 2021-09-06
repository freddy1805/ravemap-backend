const StaticPageEditor = {
    title: null,
    originalHost: '',
    slugHost: null,
    slug: null,
    locale: null,

    init: function () {
        this.title = $('.static_page_title');
        this.slugHost = $('#slug_host');
        this.originalHost = this.slugHost.text();
        this.slug = $('.static_page_slug');
        this.locale = $('select.locale_switcher');

        this._addListener();
    },

    _addListener: function () {
        console.log('addListener');

        this._setLocaleSlug();

        this.locale.on('change', () => {
            this._setLocaleSlug();
        });

        this.title.on('keyup', () => {
            this.slug.val(this._stringToSlug(this.title.val()));
        });
    },

    _initCkEditor: function () {
        let inputFields = document.querySelectorAll( 'textarea.ckeditor' );
        if (inputFields) {
            inputFields.forEach((textarea) => {
                if ($(textarea).is(":visible")) {
                    CKEDITOR.replace(textarea);
                }
            });
        }
    },

    _stringToSlug: function (str) {
        str = str.replace(/^\s+|\s+$/g, '');
        str = str.toLowerCase();

        let from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
        let to   = "aaaaeeeeiiiioooouuuunc------";
        for (let i=0, l=from.length ; i<l ; i++) {
            str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
        }

        str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
            .replace(/\s+/g, '-') // collapse whitespace and replace by -
            .replace(/-+/g, '-'); // collapse dashes

        return str;
    },

    _setLocaleSlug: function () {
        this.slugHost.text(this.originalHost + this._parseLocale() + '/');
    },

    _parseLocale: function () {
        const locale = this.locale[0].options[this.locale[0].selectedIndex].text.split('(');
        return locale[1].substr(0, locale[1].length - 1);
    }
};
