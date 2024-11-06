(function (Drupal) {
    Drupal.behaviors.mirtekSearchValidation = {
        attach: function (context, settings) {
            // Находим форму поиска, которая ещё не была обработана
            const forms = context.querySelectorAll('#search-api-form:not(.mirtek-search-validation-processed)');

            forms.forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    const minLength = 2; // Минимальная длина
                    const input = form.querySelector('input[name="s"]');
                    const searchValue = input.value.trim();

                    if (searchValue.length < minLength) {
                        // Предотвращаем отправку формы
                        event.preventDefault();

                        // Удаляем старые сообщения об ошибках
                        const oldMessages = form.parentNode.querySelectorAll('.mirtek-search-error');
                        oldMessages.forEach(function (message) {
                            message.remove();
                        });

                        // Создаем сообщение об ошибке
                        const errorMessage = document.createElement('div');
                        errorMessage.className = 'messages messages--error mirtek-search-error';
                        errorMessage.innerHTML = Drupal.t('Minimum request length: @count characters.', {'@count': minLength});

                        // Вставляем сообщение перед формой
                        form.parentNode.insertBefore(errorMessage, form);

                        // Фокусируемся на поле ввода
                        input.focus();

                        // Удаление сообщения через 2 секунды
                        setTimeout(function () {
                            errorMessage.remove();
                        }, 2000); // 2000 миллисекунд = 2 секунды
                    }
                });

                // Маркируем форму как обработанную, чтобы избежать повторного привязывания обработчика
                form.classList.add('mirtek-search-validation-processed');
            });
        }
    };
})(Drupal);
