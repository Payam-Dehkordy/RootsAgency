(function () {
    'use strict';

    var form = document.getElementById('contact-form');
    if (!form) {
        return;
    }

    form.addEventListener(
        'submit',
        function (event) {
            event.preventDefault();
            event.stopImmediatePropagation();

            if (form.classList.contains('loading') || form.classList.contains('success')) {
                return;
            }

            var data = new FormData(form);
            data.set('action', 'contact-form/send');
            form.classList.add('loading');
            form.classList.remove('success');

            fetch('/', {
                method: 'POST',
                body: data,
                headers: { Accept: 'application/json' },
            })
                .then(function (res) {
                    return res.json().then(function (body) {
                        if (!res.ok || !body.success) {
                            throw new Error('submit_failed');
                        }
                        form.classList.add('success');
                    });
                })
                .catch(function () {
                    window.alert('Something went wrong, please try again.');
                })
                .finally(function () {
                    form.classList.remove('loading');
                });
        },
        true
    );
})();
