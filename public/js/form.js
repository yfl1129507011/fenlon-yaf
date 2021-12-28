$(function () {
    $('#get-captcha').on('click', function () {
        var img = $('#captcha');
        var imgSrc = img.attr('src');
        var index = imgSrc.indexOf('?');
        if (index > 0) {
            imgSrc = imgSrc.substring(0, index);
        }
        var newSrc = imgSrc + '?' + Math.random();
        img.attr('src', newSrc);
    });

    var formCheck = {
        checkObj: null,
        feedbackMark: $("#feedback-mark"),
        check: function (object) {
            this.checkObj = object;
            var tagName = this.checkObj.prop("tagName").toLowerCase();
            switch (tagName) {
                case 'input':
                    return this.inputCheck();
                default:
                    return false;
            }
        },
        inputCheck: function(){
            var val = this.checkObj.val();
            return val ? this.validFeedback() : this.invalidFeedback();
        },
        invalidFeedback: function(){
            this.checkObj.addClass("is-invalid").focus();
            var placeholder = this.checkObj.prop("placeholder");
            if (placeholder) {
                this.feedbackMark.text("请输入"+placeholder).show();
            }
            return false;
        },
        validFeedback: function(){
            this.checkObj.removeClass("is-invalid");
            this.feedbackMark.hide();
            return true;
        },
        formFeedback: function (msg) {
            if (msg) {
                this.feedbackMark.removeClass('error').addClass('text-green').text(msg).show();
            }
        }
    };

    $("form").submit(function () {
        var isValid = false;
        $('[check-field="true"]').each(function () {
            var object = $(this);
            isValid = formCheck.check(object);
            return isValid;
        });
        console.log(isValid);
        if (isValid) {
            var that = $(this);
            $.post(that.attr("action"), that.serialize(), function (data) {
                if (data.code == 200) {
                    if (data.msg) {
                        formCheck.formFeedback(data.msg);
                        setTimeout(function() {
                            location.href = data.return_url;
                        }, 3000);
                    } else {
                        location.href = data.return_url;
                    }
                } else {
                    formCheck.formFeedback(data.msg);
                    $('#get-captcha').click();
                }
            })
        }
        return false;
    });

    $(document).keydown(function (event) {
        //alert(event.keyCode);
        if (event.keyCode == 13) {
            $("form").submit();
            return false;
        }
    })
})