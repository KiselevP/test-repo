(function ($, Drupal, drupalSettings) {

    Drupal.behaviors.mirtekProductFilter = {
        attach: function (context, settings) {
            // Скрипт для поиска по чекбоксам.
            $(".mirtek-product-filter-search", context).on("keyup", function () {
                const searchText = $(this).val().toLowerCase();

                // Обновляем выборку элементов для фильтрации с использованием контейнера.
                $(".mirtek-product-filter-container input[type='checkbox']", context).each(function () {
                    const label = $(this).closest("div").find("label").text().toLowerCase();
                    if (label.indexOf(searchText) === -1) {
                        $(this).closest("div").hide();
                    } else {
                        $(this).closest("div").show();
                    }
                });
            });

            // Проверяем состояние чекбоксов в localStorage.
            if (localStorage.getItem('mirtek-show-all') === 'true') {
                $(".mirtek-product-filter-options div", context).show(); // Показываем все чекбоксы.
                $(".mirtek-product-filter-show-more", context).hide(); // Скрываем текст "Показать ещё".
                $(".mirtek-product-filter-container", context).css("overflow-y", "auto");
            } else {
                // Скрываем все чекбоксы, кроме первых 10.
                $(".mirtek-product-filter-options div:gt(12)", context).hide();
            }

            // Обработчик для текстового элемента "Показать ещё".
            $(".mirtek-product-filter-show-more", context).on("click", function (e) {
                e.preventDefault();
                $(".mirtek-product-filter-options div:hidden", context).slideDown(); // Показываем все скрытые чекбоксы.
                $(this).hide(); // Скрываем текст "Показать ещё".
                $(".mirtek-product-filter-container", context).css("overflow-y", "auto"); // Включаем прокрутку.
                localStorage.setItem('mirtek-show-all', 'true'); // Сохраняем состояние в localStorage.
            });

            // Обработчик для чекбокса "Выбрать все".
            $(".mirtek-product-filter-options input[value='all']", context).on("change", function () {
                const isChecked = $(this).is(":checked");
                $(".mirtek-product-filter-options input[type='checkbox']", context).not("[value='all']").prop("checked", isChecked);
            });

            // Обработчик для кнопки "Применить".
            $("#edit-submit-dokumentaciya", context).on("click", function (e) {
                // Обновляем плашки после нажатия на кнопку.
                updateFilterBadges();
            });

            // Обработчик для кнопки "Сбросить".
            $("input[name='reset']", context).on("click", function (e) {
                e.preventDefault();
                localStorage.setItem('mirtek-show-all', 'false');
                window.location.reload(); // Перезагружаем страницу.
            });

            // Функция для обновления плашек фильтров.
            function updateFilterBadges() {
                const selectedFilters = [];

                // Перебираем все отмеченные чекбоксы и добавляем их значения в массив.
                $("input[type='checkbox']:checked", context).not("[value='all']").each(function () {
                    selectedFilters.push($(this).next('label').text().trim());
                });

                // Очищаем контейнер плашек.
                $('#selected-filters', context).empty();

                // Добавляем плашки для каждого выбранного фильтра.
                selectedFilters.forEach(function (filter) {
                    $('#selected-filters', context).append('<span class="filter-badge">' + filter + ' <a href="#" class="remove-filter">×</a></span>');
                });
            }

            // Событие для удаления фильтра через плашку.
            $(document).on('click', '.remove-filter', function (e) {
                e.preventDefault();
                const filterText = $(this).parent().text().trim().replace(' ×', '');
                // Находим соответствующий чекбокс и снимаем его выделение.
                $("input[type='checkbox'] + label", context).filter(function () {
                    return $(this).text().trim() === filterText;
                }).prev('input').prop('checked', false).trigger('change');

                // Обновляем плашки после удаления фильтра.
                updateFilterBadges();
            });

            // Инициализация плашек при загрузке страницы.
            updateFilterBadges();

            // Событие, которое выполняется после каждого Ajax запроса.
            $(document).ajaxComplete(function () {
                updateFilterBadges();
            });
        }
    };

})(jQuery, Drupal, drupalSettings);
