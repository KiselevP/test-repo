/**
 * @file
 * mirtekgroup behaviors.
 */
(function ($, Drupal, drupalSettings) {

  'use strict';


  Drupal.behaviors.addclassaccordion = {
    attach: function (context, settings) {
      $(document).ready(function() {
        // При клике на заголовок
        $('.accordion .title').off('click').click(function() {
          // Переключаем класс 'active' на аккордеоне
          let accordion = $(this).parent('.accordion');
          accordion.toggleClass('active');

          // Переключаем видимость div с текстом
          let textDiv = $(this).next('.content');
          if(accordion.hasClass('active')){
            textDiv.slideDown();
          } else {
            textDiv.slideUp();
          }
        });
      });
    }
  };

  Drupal.behaviors.tabslinks = {
    attach: function (context, settings) {
      const tabs = context.querySelector('.product-tabs');

      if (tabs) {
        const tabsButtons = once('tabslinks', tabs.querySelectorAll('.tab-links [data-index]'));
        const tabsPanels = tabs.querySelectorAll('.tab');

        tabsButtons.forEach(function(btn) {
          btn.addEventListener('click', function(e) {
            e.preventDefault();
            const index = btn.getAttribute('data-index');
            tabsButtons.forEach(function(button) { button.classList.remove('active'); });
            tabsPanels.forEach(function(panel) { panel.classList.remove('active'); });
            btn.classList.add('active');
            document.getElementById('tab' + index).classList.add('active');
          });
        });
      }
    }
  };


  Drupal.behaviors.copyLinkToClipboard = {
    attach: function (context, settings) {
      // При клике на иконку
      $('.otzyv-icon', context).each(function() {
        // Проверка, если уже привязан обработчик
        if (!$(this).data('copyLinkInitialized')) {
          $(this).data('copyLinkInitialized', true);

          $(this).click(function(event) {
            event.preventDefault(); // Предотвращение открытия ссылки
            event.stopPropagation(); // Предотвращение всплытия события
            // Получаем относительную ссылку из атрибута data-file родительского элемента
            let relativeLink = $(this).closest('.item').data('file');
            // Формируем полную ссылку
            let fullLink = window.location.origin + relativeLink;
            // Создаем временное поле ввода для копирования текста
            let tempInput = $('<input>');
            $('body').append(tempInput);
            tempInput.val(fullLink).select();
            document.execCommand('copy');
            tempInput.remove();
            // Показываем уведомление о копировании
            let notification = $('#copy-notification');
            notification.addClass('show messages messages--success');
            setTimeout(function() {
              notification.removeClass('show');
            }, 2000);
          });
        }
      });
    }
  };

  Drupal.behaviors.videoPreviewClick = {
    attach: function (context, settings) {
      const elements = once('videoPreviewClick', '.video-preview, .play-icon', context);
      elements.forEach(function (element) {
        element.addEventListener('click', function () {
          const videoContainer = this.closest('.video-embed');
          const videoUrl = videoContainer.querySelector('.video-preview').getAttribute('data-video-url');
          if (videoUrl) {
            const iframe = document.createElement('iframe');
            iframe.src = videoUrl + '/?autoplay=1'; // добавляем autoplay=1
            iframe.frameBorder = '0';
            iframe.allow = 'autoplay; clipboard-write; accelerometer; encrypted-media; gyroscope; picture-in-picture';
            iframe.allowFullscreen = true;
            iframe.setAttribute('webkitAllowFullScreen', '');
            iframe.setAttribute('mozallowfullscreen', '');

            // Очистка контейнера и добавление iframe
            videoContainer.innerHTML = '';
            videoContainer.appendChild(iframe);

            iframe.onload = function () {
              setTimeout(function() {
                try {
                  const setCurrentTime = JSON.stringify({ type: 'player:setCurrentTime', data: { time: 0 } });
                  iframe.contentWindow.postMessage(setCurrentTime, '*');
                  const message = JSON.stringify({ type: 'player:play', data: {} });
                  iframe.contentWindow.postMessage(message, '*');
                } catch (e) {
                  console.warn('Failed to send play command to iframe:', e);
                }
              }, 500);
            };
          }
        });
      });
    }
  };

  Drupal.behaviors.dynamicMenu = {
    attach: function (context, settings) {
      // Убедимся, что код выполняется только один раз и только на страницах с классом service-full
      if (context === document && $('.service-full', context).length) {
        if (!$('.anchor-links-wrappper').hasClass('processed')) {
          let menu = $('.anchor-links-wrappper');
          let headings = $('.content div.h3');
          let index = 0;
          let isScrolling = false; // Флаг, указывающий, идет ли анимация прокрутки
          // Очистить текущее содержимое меню
          menu.empty();
          headings.each(function (i, element) {
            // Пропустить элементы, которые содержат h2
            if ($(element).find('h2').length) {
              return true; // эквивалент continue в jQuery each
            }
            let text = $(element).text().trim();
            if (text) {
              let id = 'content-block' + (++index);
              $(element).attr('id', id);
              menu.append('<a href="#' + id + '" class="longread-anchor-link">' + text + '</a>');
            }
          });
          // Добавить класс processed, чтобы предотвратить повторное выполнение
          menu.addClass('processed');
          // Функция для определения активного заголовка и обновления активной ссылки
          function updateActiveLink() {
            if (isScrolling) return; // Если идет анимация прокрутки, выходим

            let scrollTop = $(window).scrollTop();
            let headerHeight = $('.header').outerHeight() || 0; // Высота хедера

            // Перебираем все заголовки и определяем, какой из них находится в видимой области
            headings.each(function () {
              let sectionOffset = $(this).offset().top - headerHeight - 50; // Учитываем высоту хедера
              let sectionHeight = $(this).outerHeight();

              if (scrollTop >= sectionOffset && scrollTop < sectionOffset + sectionHeight) {
                let id = $(this).attr('id');
                $('.longread-anchor-link').removeClass('active'); // Снимаем активность со всех ссылок
                $('a[href="#' + id + '"]').addClass('active'); // Добавляем активность к текущей ссылке
              }
            });
          }

          // Обновление позиции меню при скролле и изменение активной ссылки
          $(window).on('scroll', function () {
            let headerHeight = $('.header').outerHeight() || 0; // Получаем высоту хедера
            let contentOffset = $('.content').offset().top;
            let scrollTop = $(window).scrollTop();
            let contentLeftOffset = $('.content').offset().left; // Получаем отступ левой границы контента
            let contentWidth = $('.content').outerWidth(); // Получаем ширину контента

            if (scrollTop >= contentOffset - headerHeight) {
              menu.addClass('fixed').css({
                top: headerHeight + 'px', // Используем динамическую высоту хедера
                left: (contentLeftOffset + contentWidth + 15) + 'px' // Используем правильный отступ слева
              });
            } else {
              menu.removeClass('fixed').css({
                top: 'auto', // Сброс позиции, когда меню не фиксировано
                left: 'auto'
              });
            }
            // Обновляем активную ссылку при скролле
            updateActiveLink();
          });
          // Добавить обработчик для плавного скроллинга и выделения активной ссылки при клике
          $('a.longread-anchor-link').off('click').on('click', function (e) {
            e.preventDefault();
            let target = this.hash;
            let $target = $(target);
            // Убираем класс active со всех ссылок
            $('.longread-anchor-link').removeClass('active');
            // Добавляем класс active к нажатой ссылке
            $(this).addClass('active');
            // Высота фиксированного меню и хедера
            let offset = $('.anchor-links-wrappper').outerHeight();
            let headerHeight = $('.header').outerHeight() || 0; // высота хедера, если есть
            // Устанавливаем флаг, что начинается анимация прокрутки
            isScrolling = true;
            // Анимируем скролл, учитывая высоту фиксированных элементов
            $('html, body').stop().animate({
              'scrollTop': $target.offset().top - offset + headerHeight
            }, 900, 'swing', function() {
              // Сбрасываем флаг после завершения анимации прокрутки
              isScrolling = false;
              updateActiveLink(); // Обновляем активную ссылку после завершения прокрутки
            });
          });
        }
      }
    }
  };


  // Кнопка поделиться в FAQ
  Drupal.behaviors.copyShareLink = {
    attach: function (context, settings) {
      window.copyShareLink = function (nodeId) {
        // Используем упрощенный формат якоря с id
        const url = window.location.origin + '/faq#' + nodeId;

        // Копируем ссылку в буфер обмена
        navigator.clipboard.writeText(url).then(function() {
          // Создаем элемент для сообщения
          let message = $('<div class="copy-share messages messages--success"></div>').text(Drupal.t('Link copied'));

          // Добавляем сообщение в body
          $('body').append(message);

          // Удаляем сообщение через 2 секунды
          setTimeout(function() {
            message.remove();
          }, 2000);
        }, function(err) {
          console.error('Не удалось скопировать ссылку: ', err);
        });
      };
    }
  };

  Drupal.behaviors.addclasscontent = {
    attach: function (context, settings) {
      $(document).ready(function() {
        // Проверяем URL на наличие упрощенного якоря и активируем соответствующий вопрос
        const hash = window.location.hash;
        if (hash && hash.startsWith('#')) {
          const targetId = hash.substring(1); // Извлекаем идентификатор, убирая #

          // Закрываем все вопросы
          $('.faq-full').removeClass('active');
          $('.faq-full .content').hide(); // Мгновенно скрываем все вопросы

          // Открываем целевой вопрос
          const targetQuestion = $('[data-history-node-id="' + targetId + '"]');
          if (targetQuestion.length) {
            targetQuestion.addClass('active');
            targetQuestion.find('.content').show(); // Мгновенно раскрываем нужный вопрос
          }
        }
        // При клике на заголовок
        $('.faq-full .title').off('click').click(function() {
          let content = $(this).parent('.faq-full');
          content.toggleClass('active');

          // Переключаем видимость div с текстом
          let contentDiv = $(this).next('.content');
          if(content.hasClass('active')) {
            contentDiv.slideDown();
          } else {
            contentDiv.slideUp();
          }
        });
      });
    }
  };

  Drupal.behaviors.ContactsAndMap = {
    attach: function (context, settings) {
      // Инициализация карты и установка флага
      if (!this.initialized) {
        this.initialized = true; // Устанавливаем флаг инициализации
        // Изначально скрываем все элементы .paragraphs
        $('.view-contacts .views-row .paragraphs', context).removeClass('open').addClass('closed');
        // Добавляем класс 'active' первому блоку node.contact по умолчанию при первой загрузке страницы
        $('.view-contacts .views-row .node.contact:first', context).addClass('active');
        // Показываем только тот .paragraphs, который связан с первым активным блоком node.contact
        $('.view-contacts .views-row .node.contact.active', context).next('.paragraphs').removeClass('closed').addClass('open');
        // Проверяем, существует ли уже карта или загружается ли уже скрипт карты
        if (!$('#map').data('initialized') && $('script[src*="https://api-maps.yandex.ru/2.1/?lang=ru"]').length === 0) {
          $('#map').data('initialized', true);
          let myMap; // Переменная, которая может изменяться
          const mapData = {}; // Объект для хранения данных карты
          // Собираем данные для карты из атрибутов data-* HTML элементов
          $('.address', context).each(function () {
            const companyCode = $(this).attr('data-company');
            const coordinates = $(this).attr('data-coordinates');
            const bodyContent = $(this).attr('data-body');
            const noteContent = $(this).attr('data-note');

            if (coordinates) {
              const coords = coordinates.split(',').map(parseFloat);
              mapData[companyCode] = {
                coords: coords,
                city: $(this).attr('data-city'),
                company: companyCode,
                body: bodyContent,
                note: noteContent
              };
            }
            // else {
            //   console.error('No coordinates found for company:', companyCode);
            // }
          });

          // Функция для получения кода текущей активной компании
          function getActiveCompanyCode() {
            const activeElement = $('.node.contact.active .address', context);
            if (activeElement.length) {
              return activeElement.attr('data-company');
            }
            return null;
          }

          // Инициализация карты с активной компанией
          const initialCompanyCode = getActiveCompanyCode();
          // Если начальная компания не определена, возвращаем
          // if (!initialCompanyCode || !mapData[initialCompanyCode]) {
          //   console.error("Начальные координаты не определены или данные отсутствуют.");
          //   return;
          // }

          // Функция для смены адреса
          function changeAddress(companyCode) {
            if (mapData[companyCode]) {
              makeMarker(myMap, companyCode);
              myMap.panTo(mapData[companyCode].coords, { duration: 500 });
            }
            // else {
            //   console.error('Invalid company code:', companyCode);
            // }
          }

          // Функция для создания маркера на карте
          function makeMarker(map, arg) {
            const noteClass = 'data-note';
            const mark = new ymaps.Placemark(
                mapData[arg].coords,
                {
                  balloonContentHeader: mapData[arg].city,
                  balloonContentBody: `
                    <div>
                      ${mapData[arg].company}<br>
                      ${mapData[arg].body}<br>
                      <span class="${noteClass}">${mapData[arg].note}</span>
                    </div>`,
                  hintContent: mapData[arg].company
                },
                {
                  iconLayout: 'default#image',
                  iconImageSize: [44, 44],
                  iconImageOffset: [-15, -46]
                }
            );
            map.geoObjects.removeAll(); // Удаляем предыдущие маркеры
            map.geoObjects.add(mark);
            mark.balloon.open();
            map.setCenter(mapData[arg].coords);
          }

          // Функция для инициализации карты
          function initMap() {
            if (mapData[initialCompanyCode]) {
              myMap = new ymaps.Map('map', {
                center: mapData[initialCompanyCode].coords,
                zoom: 18,
                controls: ['smallMapDefaultSet']
              });
              makeMarker(myMap, initialCompanyCode);
              myMap.container.fitToViewport();
            }
          }

          // Функция для включения скрипта Яндекс.Карт
          function includeMapsScript() {
            const scriptTag = document.createElement('script');
            scriptTag.setAttribute('src', 'https://api-maps.yandex.ru/2.1/?lang=ru&apikey=ВАШ_API_КЛЮЧ');
            document.body.appendChild(scriptTag);

            scriptTag.addEventListener('load', onMapLoad);
          }

          // Функция загрузки карты
          function onMapLoad() {
            ymaps.ready(initMap);
          }

          // Инициализация карты при загрузке страницы
          $(document).ready(function () {
            includeMapsScript();

            // Обработчик события клика для изменения компании
            $('.node.contact', context).on('click', function () {
              const newCompanyCode = getActiveCompanyCode();
              changeAddress(newCompanyCode);
            });
          });
        }
      }

      // Обработчик события клика для раскрытия параграфов
      $('.view-contacts .views-row .node.contact', context).off('click').on('click', function () {
        const paragraphs = $(this).next('.paragraphs');

        // Если текущий блок уже активен
        if ($(this).hasClass('active')) {
          // Убираем класс 'active' и скрываем параграфы
          paragraphs.removeClass('open').addClass('closed');
          $(this).removeClass('active');
        } else {
          // Убираем класс 'active' у всех блоков node.contact и скрываем все элементы .paragraphs
          $('.view-contacts .views-row .node.contact', context).removeClass('active');
          $('.view-contacts .views-row .paragraphs', context).removeClass('open').addClass('closed');

          // Добавляем класс 'active' к текущему блоку node.contact
          $(this).addClass('active');

          // Устанавливаем класс 'open' для анимации раскрытия
          paragraphs.removeClass('closed').addClass('open');
        }
      });
    }
  };

})(jQuery, Drupal, drupalSettings);

