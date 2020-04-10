var awards = {
    all_sections : [],
    init : function(sections) {
        var i, zone;
        awards.all_sections = sections;
        for (i in awards.all_sections) {
            zone = awards.all_sections[i];
            $('#toggle_' + zone)
                .css({ 'cursor' : 'pointer' })
                .prop('title', msg.show_hide)
                .click(
                    function() {
                        $('#' + this.id.replace('toggle_','')).toggle();
                        $(this).find('span').toggle();
                    }
                )
                .find('span').css({'font-size': '120%'});
        }
        $('#all_0').click(function() { awards.toggleSections(0); return false; });
        $('#all_1').click(function() { awards.toggleSections(1); return false; });
        $('#form_email').change(function() {
            if (isValidEmail($('#form_email').val())) {
                $('#form_submit').removeAttr('disabled');
            } else {
                $('#form_submit').attr('disabled', 'disabled');
            }
        });
        $('#form_done').click(function() {
            location.replace(location.protocol + '//' + location.host + location.pathname );
        });
        $('#form_body').val(msg.cart_none);
        $('.cart').each(function() {
            $(this).html(
                '<span>' +
                '<img src="' + base_image + '/icon_cart.gif" alt="' + msg.no + '" title=""/>' +
                '<img style="display: none" src="' + base_image + '/icon_cart_added.gif" alt="' + msg.yes + '" />' +
                '</span>'
            );
        });
        $('.cart span').click(function() {
            var p = $(this).parent();
            var id = p.attr('id');
            awards.toggleAward(id);
            p.find('img').toggle();
        });
        $('#form_submit').click(function() {
            var message = msg.cart_conf_1 + '\n' + msg.cart_conf_2 + '\n\n' + msg.cart_conf_3 + '\n' + msg.cart_conf_4;
            if (!confirm(message)) {
                alert(msg.cancelled);
                return false;
            }
        });
    },
    toggleAward : function(id) {
        var awards, i, idx, len, message;
        len = 8;
        idx = $.inArray(id, cart);
        if (idx === -1) {
            cart.push(id);
        } else {
            cart.splice(idx, 1)
        }
        cart = cart.sort();
        message = msg.cart_none;
        if (cart.length) {
            awards = [];
            for (i in cart) {
                awards.push(cart[i].split('-')[0]);
            }
            awards = $.grep(awards, function(v, k){
                return $.inArray(v ,awards) === k;
            });
            message =
                msg.cart_1 + '\n' +
                msg.cart_2.padEnd(len, ' ') + award.admin + '\n' +
                msg.cart_3.padEnd(len, ' ') + award.from + '\n' +
                msg.cart_4.padEnd(len, ' ') + award.url + '/' + awards.join(',') + '\n' +
                '\n' +
                msg.cart_5 + '\n' +
                msg.cart_6 + '\n\n' +
                ' * ' + cart.join('\n * ') + '\n\n' +
                msg.cart_7 + '\n' +
                award.name;
        }
        $('#form_awards').val(cart.join(','));
        $('#form_filter').val(awards.join(','));
        $('#form_body').val(message);
        if (cart.length && isValidEmail($('#form_email').val())) {
            $('#form_submit').removeAttr('disabled');
        } else {
            $('#form_submit').attr('disabled', 'disabled');
        }
    },
    toggleSections : function(show) {
        var i, section, sectionToggle;
        for (i in awards.all_sections) {
            section = $('#' + awards.all_sections[i]);
            sectionToggle = $('#toggle_' + awards.all_sections[i]);
            if (show) {
                section.show();
                sectionToggle.find('span:eq(0)').hide();
                sectionToggle.find('span:eq(1)').show();
            } else {
                section.hide();
                $sectionToggle.find('span:eq(0)').show();
                sectionToggle.find('span:eq(1)').hide();
            }
        }
    }
};