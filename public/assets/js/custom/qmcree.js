var qmcree = {
    CONTACT_ALERT_SELECTOR: '#form-contact-message',
    CONTACT_FORM_SELECTOR: '#form-contact',
    CONTACT_CAPTCHA_ELEMENT_ID: 'form-contact-captcha',
    CONTACT_CAPTCHA_SELECTOR: '#form-contact-captcha',
    CONTACT_SUBMIT_SELECTOR: '#submit-contact',
    CONTACT_MSG_ERROR_ONE: "There's a problem with the highlighted field.",
    CONTACT_MSG_ERROR_MANY: "There are problems with the highlighted fields.",
    CONTACT_MSG_ERROR_REQUEST: "Something's wrong. Please email or call me directly.",
    RECAPTCHA_PUBLIC_KEY: '6LfC6vASAAAAAF3sHK4LE_ghnbhDagp3FYLwbtaw',
    PORTFOLIO_SELECTOR: '.bold-portfolio',
    POPUP_IMAGE_SELECTOR: '.image-popup',
    POPUP_VIDEO_SELECTOR: '.video-popup',
    PROJECT_SELECTOR: '.project-wrap',
    PROJECT_LINKS_SELECTOR: '.project-links',
    ALERT_SUCCESS: 1,
    ALERT_DANGER: 2,

    captchaVisible: false,

    alert: function(type, message) {
        var element = jQuery(this.CONTACT_ALERT_SELECTOR).removeClass('success danger');

        if (type === this.ALERT_SUCCESS) {
            element.addClass('success');
        } else if (type === this.ALERT_DANGER) {
            element.addClass('danger');
            message = '<strong>Uh oh!</strong> ' + message;
        }
        element.html(message).slideDown(100);
    },
    hideAlert: function() {
        jQuery(this.CONTACT_ALERT_SELECTOR).hide().html('');
    },
    contact: function() {
        var that = this,
            form = jQuery(this.CONTACT_FORM_SELECTOR),
            submitBtn = jQuery(this.CONTACT_SUBMIT_SELECTOR);

        submitBtn.button('loading');

        jQuery.ajax('/ajax/contact.php', {
            data: jQuery(this.CONTACT_FORM_SELECTOR).serialize(),
            dataType: 'json',
            complete: function() {
                Recaptcha.reload();
                submitBtn.button('reset');
            },
            error: function() {
                // request failed.
                that.alert(that.ALERT_DANGER, that.CONTACT_MSG_ERROR_REQUEST);
            },
            success: function(data) {
                if (data.type === 'success') {
                    that.alert(that.ALERT_SUCCESS, "<strong>Thanks!</strong> I will be in contact with you soon.");
                    form.get(0).reset();
                } else {
                    that.alert(that.ALERT_DANGER, data.message);
                }
            },
            type: 'POST'
        });
    },
    init: function() {
        var that = this;

        function contact() {
            var form = jQuery(that.CONTACT_FORM_SELECTOR),
                captcha = jQuery(that.CONTACT_CAPTCHA_SELECTOR);

            form.validate({
                rules: {
                    recaptcha_response_field: {
                        required: true,
                        depends: that.captchaVisible
                    }
                },
                errorPlacement: function(error, element) {
                    // override, do nothing.
                },
                invalidHandler: function(e, validator) {
                    if (validator.numberOfInvalids() === 1) {
                        that.alert(that.ALERT_DANGER, that.CONTACT_MSG_ERROR_ONE);
                    } else {
                        that.alert(that.ALERT_DANGER, that.CONTACT_MSG_ERROR_MANY);
                    }
                },
                submitHandler: function() {
                    // overrides validated submission behavior.
                    that.hideAlert();

                    if (!that.captchaVisible) {
                        captcha.slideDown(200);
                        Recaptcha.focus_response_field();
                        that.captchaVisible = true;
                    } else {
                        that.contact();
                    }
                }
            });

            // trigger submit event when button clicked.
            jQuery(that.CONTACT_SUBMIT_SELECTOR).click(function(e) {
                e.preventDefault();

                form.trigger('submit');
            });

            Recaptcha.create(that.RECAPTCHA_PUBLIC_KEY, that.CONTACT_CAPTCHA_ELEMENT_ID, { theme: 'custom' });
        }

        function portfolio() {
            jQuery(that.POPUP_IMAGE_SELECTOR).magnificPopup({type: 'image' });
            jQuery(that.POPUP_VIDEO_SELECTOR).magnificPopup({type: 'iframe' }); // Supports YouTube, Vimeo and Google Maps links.
            jQuery(that.PROJECT_SELECTOR).hover(
                function() {
                    jQuery(this).find(that.PROJECT_LINKS_SELECTOR).animate({ top: 0 }, 'fast');
                },
                function() {
                    jQuery(this).find(that.PROJECT_LINKS_SELECTOR).animate({ top: 100 + '%' }, 'fast');
                }
            );
        }

        function background() {
            jQuery.backstretch("/assets/img/backstretch.jpg");
        }

        function spamProofEmail() {
            var parts = ['com', 'g', 'mail', '@', 'mcr', 'ee', 'q', '.'],
                email = parts[6] + parts[4] + parts[5] + parts[3] + parts[1] + parts[2] + parts[7] + parts[0];
            jQuery('#email-text').html('<a href="mailto:' + email + '">' + email + '</a>');
        }

        background();
        contact();
        spamProofEmail();
        portfolio();
    }
};

jQuery(document).ready(function() {
    qmcree.init();
});