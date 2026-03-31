//UTILS START
var ptsUtils = {
  slidesEditWnd: null,
  addMenuItemWnd: null,
  addMenuItemWndBlock: null,
  subSettingsWnd: null,
  subSettingsWndBlock: null,
  subAddFieldWnd: null,
  subAddFieldWndBlock: null,
  iconsLibWnd: null,
  iconsLibWndElement: null,
  badgesLibWnd: null,
  badgesLibWndElement: null,
  colorPicker: null,
  showSlidesEditWnd: function (block) {
    var self = this;
    if (!this.slidesEditWnd) {
      this.slidesEditWnd = jQuery('#ptsManageSlidesWnd').dialog('close');
      this.slidesEditWnd.find('.ptsManageSlidesSaveBtn').click(function () {
        block.beforeSave();
        var listPrev = self.slidesEditWnd.find('.ptsSlidesListPrev'),
          slides = block.getSlides(),
          sliderShell = block.getSliderShell(),
          tmpDiv = jQuery('<div style="display: none;" />').appendTo('body');
        listPrev.find('.ptsSlideManageItem').each(function () {
          var slideId = jQuery(this).data('slide-id');
          slides.each(function () {
            if (jQuery(this).data('slide-id') == slideId) {
              tmpDiv.append(jQuery(this));
              return false;
            }
          });
        });
        sliderShell.html('').append(tmpDiv.find(':data(slide-id)'));
        tmpDiv.remove();
        block.afterSave();
        _ptsSaveCanvas();
        self.slidesEditWnd.dialog('close');
        return false;
      });
      this.slidesEditWnd.find('.ptsSlideManageAddBtn').click(function () {
        // Simulate click on Add slide menu btn
        block._clickMenuItem_add_slide(
          {},
          {
            clb: function () {
              self.showSlidesEditWnd(block);
            },
          }
        );
        if (this.slidesEditWnd) this.slidesEditWnd.dialog('close');
        return false;
      });
    }
    var listPrev = this.slidesEditWnd.find('.ptsSlidesListPrev');
    listPrev.find('*:not(.ptsSlideManageAddBtn)').remove();
    var slides = block.getSlides();
    if (slides && slides.length) {
      slides.each(function () {
        var newItem = jQuery('#ptsSlideManageItemExl').clone().removeAttr('id');
        newItem.find('img:first').attr('src', jQuery(this).find('.ptsSlideImg').attr('src'));
        newItem.data('slide-id', jQuery(this).data('slide-id'));
        listPrev.prepend(newItem);
        newItem.find('.ptsSlideManageItemRemove').click(function () {
          //if(confirm(toeLangPts('Are you sure want to remove this slide?'))) {
          jQuery(this)
            .parents('.ptsSlideManageItem:first')
            .hide(g_ptsAnimationSpeed, function () {
              jQuery(this).remove();
            });
          //}
          return false;
        });
      });
      listPrev.sortable({
        revert: true,
        items: '.ptsSlideManageItem',
        placeholder: 'ui-state-highlight',
        //,	axis: 'x'
      });
      listPrev.find('*').disableSelection();
    } else {
      listPrev.prepend('<div>' + toeLangPts('You have no slides for now - try to add them at first.') + '</div>');
    }
    this.slidesEditWnd.dialog('open');
  },
  _getEllIconsLibHtml: function () {
    return this.iconsLibWnd.find('.ptsIconsLibList .ptsIconLibItem');
  },
  _showAllIconsLib: function () {
    this._getEllIconsLibHtml().show();
  },
  initIconsLibWnd: function () {
    var self = this;
    this.iconsLibWnd = jQuery('#ptsIconsLibWnd').dialog({
      resizable: false,
      closeText: '',
      height: 'auto',
      width: '90%',
      title: 'ICONS LIBRARY',
      modal: true,
    });
    this.iconsLibWnd.find('.ptsIconsLibSearchTxt').keyup(function () {
      var value = jQuery.trim(jQuery(this).val());
      if (value && value != '') {
        var keys = jQuery(this).val().split(' '),
          allFoundIcons = self._getEllIconsLibHtml(),
          initialSize = allFoundIcons.length;
        allFoundIcons.show();
        for (var i = 0; i < keys.length; i++) {
          allFoundIcons = allFoundIcons.not('[data-icon*="' + keys[i] + '"]');
        }
        allFoundIcons.hide();
        if (initialSize == allFoundIcons.length) {
          // Anything was found
          self._showNothingFoundIconsLib(value);
        }
      } else {
        self._hideNothingFoundIconsLib();
        self._showAllIconsLib();
      }
      return false;
    });
    this.iconsLibWnd.find('.ptsIconsLibSaveBtn').click(function () {
      ptsUtils.iconsLibWnd.dialog('close');
      return false;
    });
    var allIcons = this.getFaIconsList(),
      iconsShell = this.iconsLibWnd.find('.ptsIconsLibList');
    iconsShell.html('');
    for (var i = 0; i < allIcons.length; i++) {
      var iconName = this._faIconClassToName(allIcons[i]);
      iconsShell.append(
        '<div class="ptsIconLibItem supMd3 supSm4" onclick="ptsUtils.selectFaIconFromLib(this); return false;" data-icon="' + allIcons[i] + '" data-name="' + iconName + '">' + '<i class="ptsIconLibPrev fa ' + allIcons[i] + '"></i>' + '<span class="ptsIconLibTitle">' + iconName + '</span>' + '</div>'
      );
    }
  },
  selectFaIconFromLib: function (clickIcon) {
    if (this.iconsLibWndElement) {
      var prevClass = this.iconsLibWndElement.get('icon'),
        newClass = jQuery(clickIcon).data('icon');
      this.iconsLibWndElement._getEditArea().removeClass(prevClass).addClass(newClass);
      this.iconsLibWndElement.set('icon', newClass);
      _ptsSaveCanvas();
    } else console.error('Can not find element for icon apply!!!');
    this.iconsLibWnd.dialog('close');
  },
  _faIconClassToName: function (str) {
    return str.substr(3);
  },
  _showNothingFoundIconsLib: function (keys) {
    var msgEl = this.iconsLibWnd.find('.ptsIconsLibEmptySearch');
    if (keys) {
      msgEl.find('.ptsNothingFoundKeys').html(keys);
    }
    msgEl.slideDown(g_ptsAnimationSpeed);
  },
  _hideNothingFoundIconsLib: function () {
    this.iconsLibWnd.find('.ptsIconsLibEmptySearch').hide();
  },
  showIconsLibWnd: function (element) {
    if (!this.iconsLibWnd) {
      this.initIconsLibWnd();
    }
    this.iconsLibWndElement = element;
    this._showAllIconsLib();
    this._hideNothingFoundIconsLib();
    this.iconsLibWnd.find('.ptsIconsLibSearchTxt').val('');
    this.iconsLibWnd.dialog('open');
  },
  converUrl: function (url) {
    if (url.indexOf('http') !== 0) {
      url = 'http://' + url;
    }
    return url;
  },
  urlToVideoSrc: function (url) {
    var src = '';
    if ((src = url.replace(/.*www\.youtube\.com\/watch\?v\=(.+)/gi, '$1')) !== url) {
      return 'https://www.youtube.com/embed/' + src;
    } else if ((src = url.replace(/.*vimeo\.com.*(\d+)/gi, '$1')) !== url) {
      return 'https://player.vimeo.com/video/' + src + '?badge=0';
    }
    return url;
  },
  getFaIconsList: function () {
    return [
      'fa-adjust',
      'fa-adn',
      'fa-align-center',
      'fa-align-justify',
      'fa-align-left',
      'fa-align-right',
      'fa-ambulance',
      'fa-anchor',
      'fa-android',
      'fa-angellist',
      'fa-angle-double-down',
      'fa-angle-double-left',
      'fa-angle-double-right',
      'fa-angle-double-up',
      'fa-angle-down',
      'fa-angle-left',
      'fa-angle-right',
      'fa-angle-up',
      'fa-apple',
      'fa-archive',
      'fa-area-chart',
      'fa-arrow-circle-down',
      'fa-arrow-circle-left',
      'fa-arrow-circle-o-down',
      'fa-arrow-circle-o-left',
      'fa-arrow-circle-o-right',
      'fa-arrow-circle-o-up',
      'fa-arrow-circle-right',
      'fa-arrow-circle-up',
      'fa-arrow-down',
      'fa-arrow-left',
      'fa-arrow-right',
      'fa-arrow-up',
      'fa-arrows',
      'fa-arrows-alt',
      'fa-arrows-h',
      'fa-arrows-v',
      'fa-asterisk',
      'fa-at',
      'fa-automobile(alias)',
      'fa-backward',
      'fa-ban',
      'fa-bank(alias)',
      'fa-bar-chart',
      'fa-bar-chart-o(alias)',
      'fa-barcode',
      'fa-bars',
      'fa-bed',
      'fa-beer',
      'fa-behance',
      'fa-behance-square',
      'fa-bell',
      'fa-bell-o',
      'fa-bell-slash',
      'fa-bell-slash-o',
      'fa-bicycle',
      'fa-binoculars',
      'fa-birthday-cake',
      'fa-bitbucket',
      'fa-bitbucket-square',
      'fa-bitcoin(alias)',
      'fa-bold',
      'fa-bolt',
      'fa-bomb',
      'fa-book',
      'fa-bookmark',
      'fa-bookmark-o',
      'fa-briefcase',
      'fa-btc',
      'fa-bug',
      'fa-building',
      'fa-building-o',
      'fa-bullhorn',
      'fa-bullseye',
      'fa-bus',
      'fa-buysellads',
      'fa-cab(alias)',
      'fa-calculator',
      'fa-calendar',
      'fa-calendar-o',
      'fa-camera',
      'fa-camera-retro',
      'fa-car',
      'fa-caret-down',
      'fa-caret-left',
      'fa-caret-right',
      'fa-caret-square-o-down',
      'fa-caret-square-o-left',
      'fa-caret-square-o-right',
      'fa-caret-square-o-up',
      'fa-caret-up',
      'fa-cart-arrow-down',
      'fa-cart-plus',
      'fa-cc',
      'fa-cc-amex',
      'fa-cc-discover',
      'fa-cc-mastercard',
      'fa-cc-paypal',
      'fa-cc-stripe',
      'fa-cc-visa',
      'fa-certificate',
      'fa-chain(alias)',
      'fa-chain-broken',
      'fa-check',
      'fa-check-circle',
      'fa-check-circle-o',
      'fa-check-square',
      'fa-check-square-o',
      'fa-chevron-circle-down',
      'fa-chevron-circle-left',
      'fa-chevron-circle-right',
      'fa-chevron-circle-up',
      'fa-chevron-down',
      'fa-chevron-left',
      'fa-chevron-right',
      'fa-chevron-up',
      'fa-child',
      'fa-circle',
      'fa-circle-o',
      'fa-circle-o-notch',
      'fa-circle-thin',
      'fa-clipboard',
      'fa-clock-o',
      'fa-close(alias)',
      'fa-cloud',
      'fa-cloud-download',
      'fa-cloud-upload',
      'fa-cny(alias)',
      'fa-code',
      'fa-code-fork',
      'fa-codepen',
      'fa-coffee',
      'fa-cog',
      'fa-cogs',
      'fa-columns',
      'fa-comment',
      'fa-comment-o',
      'fa-comments',
      'fa-comments-o',
      'fa-compass',
      'fa-compress',
      'fa-connectdevelop',
      'fa-copy(alias)',
      'fa-copyright',
      'fa-credit-card',
      'fa-crop',
      'fa-crosshairs',
      'fa-css3',
      'fa-cube',
      'fa-cubes',
      'fa-cut(alias)',
      'fa-cutlery',
      'fa-dashboard(alias)',
      'fa-dashcube',
      'fa-database',
      'fa-dedent(alias)',
      'fa-delicious',
      'fa-desktop',
      'fa-deviantart',
      'fa-diamond',
      'fa-digg',
      'fa-dollar(alias)',
      'fa-dot-circle-o',
      'fa-download',
      'fa-dribbble',
      'fa-dropbox',
      'fa-drupal',
      'fa-edit(alias)',
      'fa-eject',
      'fa-ellipsis-h',
      'fa-ellipsis-v',
      'fa-empire',
      'fa-envelope',
      'fa-envelope-o',
      'fa-envelope-square',
      'fa-eraser',
      'fa-eur',
      'fa-euro(alias)',
      'fa-exchange',
      'fa-exclamation',
      'fa-exclamation-circle',
      'fa-exclamation-triangle',
      'fa-expand',
      'fa-external-link',
      'fa-external-link-square',
      'fa-eye',
      'fa-eye-slash',
      'fa-eyedropper',
      'fa-facebook',
      'fa-facebook-f(alias)',
      'fa-facebook-official',
      'fa-facebook-square',
      'fa-fast-backward',
      'fa-fast-forward',
      'fa-fax',
      'fa-female',
      'fa-fighter-jet',
      'fa-file',
      'fa-file-archive-o',
      'fa-file-audio-o',
      'fa-file-code-o',
      'fa-file-excel-o',
      'fa-file-image-o',
      'fa-file-movie-o(alias)',
      'fa-file-o',
      'fa-file-pdf-o',
      'fa-file-photo-o(alias)',
      'fa-file-picture-o(alias)',
      'fa-file-powerpoint-o',
      'fa-file-sound-o(alias)',
      'fa-file-text',
      'fa-file-text-o',
      'fa-file-video-o',
      'fa-file-word-o',
      'fa-file-zip-o(alias)',
      'fa-files-o',
      'fa-film',
      'fa-filter',
      'fa-fire',
      'fa-fire-extinguisher',
      'fa-flag',
      'fa-flag-checkered',
      'fa-flag-o',
      'fa-flash(alias)',
      'fa-flask',
      'fa-flickr',
      'fa-floppy-o',
      'fa-folder',
      'fa-folder-o',
      'fa-folder-open',
      'fa-folder-open-o',
      'fa-font',
      'fa-forumbee',
      'fa-forward',
      'fa-foursquare',
      'fa-frown-o',
      'fa-futbol-o',
      'fa-gamepad',
      'fa-gavel',
      'fa-gbp',
      'fa-ge(alias)',
      'fa-gear(alias)',
      'fa-gears(alias)',
      'fa-genderless(alias)',
      'fa-gift',
      'fa-git',
      'fa-git-square',
      'fa-github',
      'fa-github-alt',
      'fa-github-square',
      'fa-gittip(alias)',
      'fa-glass',
      'fa-globe',
      'fa-google',
      'fa-google-plus',
      'fa-google-plus-square',
      'fa-google-wallet',
      'fa-graduation-cap',
      'fa-gratipay',
      'fa-group(alias)',
      'fa-h-square',
      'fa-hacker-news',
      'fa-hand-o-down',
      'fa-hand-o-left',
      'fa-hand-o-right',
      'fa-hand-o-up',
      'fa-hdd-o',
      'fa-header',
      'fa-headphones',
      'fa-heart',
      'fa-heart-o',
      'fa-heartbeat',
      'fa-history',
      'fa-home',
      'fa-hospital-o',
      'fa-hotel(alias)',
      'fa-html5',
      'fa-ils',
      'fa-image(alias)',
      'fa-inbox',
      'fa-indent',
      'fa-info',
      'fa-info-circle',
      'fa-inr',
      'fa-instagram',
      'fa-institution(alias)',
      'fa-ioxhost',
      'fa-italic',
      'fa-joomla',
      'fa-jpy',
      'fa-jsfiddle',
      'fa-key',
      'fa-keyboard-o',
      'fa-krw',
      'fa-language',
      'fa-laptop',
      'fa-lastfm',
      'fa-lastfm-square',
      'fa-leaf',
      'fa-leanpub',
      'fa-legal(alias)',
      'fa-lemon-o',
      'fa-level-down',
      'fa-level-up',
      'fa-life-bouy(alias)',
      'fa-life-buoy(alias)',
      'fa-life-ring',
      'fa-life-saver(alias)',
      'fa-lightbulb-o',
      'fa-line-chart',
      'fa-link',
      'fa-linkedin',
      'fa-linkedin-square',
      'fa-linux',
      'fa-list',
      'fa-list-alt',
      'fa-list-ol',
      'fa-list-ul',
      'fa-location-arrow',
      'fa-lock',
      'fa-long-arrow-down',
      'fa-long-arrow-left',
      'fa-long-arrow-right',
      'fa-long-arrow-up',
      'fa-magic',
      'fa-magnet',
      'fa-mail-forward(alias)',
      'fa-mail-reply(alias)',
      'fa-mail-reply-all(alias)',
      'fa-male',
      'fa-map-marker',
      'fa-mars',
      'fa-mars-double',
      'fa-mars-stroke',
      'fa-mars-stroke-h',
      'fa-mars-stroke-v',
      'fa-maxcdn',
      'fa-meanpath',
      'fa-medium',
      'fa-medkit',
      'fa-meh-o',
      'fa-mercury',
      'fa-microphone',
      'fa-microphone-slash',
      'fa-minus',
      'fa-minus-circle',
      'fa-minus-square',
      'fa-minus-square-o',
      'fa-mobile',
      'fa-mobile-phone(alias)',
      'fa-money',
      'fa-moon-o',
      'fa-mortar-board(alias)',
      'fa-motorcycle',
      'fa-music',
      'fa-navicon(alias)',
      'fa-neuter',
      'fa-newspaper-o',
      'fa-openid',
      'fa-outdent',
      'fa-pagelines',
      'fa-paint-brush',
      'fa-paper-plane',
      'fa-paper-plane-o',
      'fa-paperclip',
      'fa-paragraph',
      'fa-paste(alias)',
      'fa-pause',
      'fa-paw',
      'fa-paypal',
      'fa-pencil',
      'fa-pencil-square',
      'fa-pencil-square-o',
      'fa-phone',
      'fa-phone-square',
      'fa-photo(alias)',
      'fa-picture-o',
      'fa-pie-chart',
      'fa-pied-piper',
      'fa-pied-piper-alt',
      'fa-pinterest',
      'fa-pinterest-p',
      'fa-pinterest-square',
      'fa-plane',
      'fa-play',
      'fa-play-circle',
      'fa-play-circle-o',
      'fa-plug',
      'fa-plus',
      'fa-plus-circle',
      'fa-plus-square',
      'fa-plus-square-o',
      'fa-power-off',
      'fa-print',
      'fa-puzzle-piece',
      'fa-qq',
      'fa-qrcode',
      'fa-question',
      'fa-question-circle',
      'fa-quote-left',
      'fa-quote-right',
      'fa-ra(alias)',
      'fa-random',
      'fa-rebel',
      'fa-recycle',
      'fa-reddit',
      'fa-reddit-square',
      'fa-refresh',
      'fa-remove(alias)',
      'fa-renren',
      'fa-reorder(alias)',
      'fa-repeat',
      'fa-reply',
      'fa-reply-all',
      'fa-retweet',
      'fa-rmb(alias)',
      'fa-road',
      'fa-rocket',
      'fa-rotate-left(alias)',
      'fa-rotate-right(alias)',
      'fa-rouble(alias)',
      'fa-rss',
      'fa-rss-square',
      'fa-rub',
      'fa-ruble(alias)',
      'fa-rupee(alias)',
      'fa-save(alias)',
      'fa-scissors',
      'fa-search',
      'fa-search-minus',
      'fa-search-plus',
      'fa-sellsy',
      'fa-send(alias)',
      'fa-send-o(alias)',
      'fa-server',
      'fa-share',
      'fa-share-alt',
      'fa-share-alt-square',
      'fa-share-square',
      'fa-share-square-o',
      'fa-shekel(alias)',
      'fa-sheqel(alias)',
      'fa-shield',
      'fa-ship',
      'fa-shirtsinbulk',
      'fa-shopping-cart',
      'fa-sign-in',
      'fa-sign-out',
      'fa-signal',
      'fa-simplybuilt',
      'fa-sitemap',
      'fa-skyatlas',
      'fa-skype',
      'fa-slack',
      'fa-sliders',
      'fa-slideshare',
      'fa-smile-o',
      'fa-soccer-ball-o(alias)',
      'fa-sort',
      'fa-sort-alpha-asc',
      'fa-sort-alpha-desc',
      'fa-sort-amount-asc',
      'fa-sort-amount-desc',
      'fa-sort-asc',
      'fa-sort-desc',
      'fa-sort-down(alias)',
      'fa-sort-numeric-asc',
      'fa-sort-numeric-desc',
      'fa-sort-up(alias)',
      'fa-soundcloud',
      'fa-space-shuttle',
      'fa-spinner',
      'fa-spoon',
      'fa-spotify',
      'fa-square',
      'fa-square-o',
      'fa-stack-exchange',
      'fa-stack-overflow',
      'fa-star',
      'fa-star-half',
      'fa-star-half-empty(alias)',
      'fa-star-half-full(alias)',
      'fa-star-half-o',
      'fa-star-o',
      'fa-steam',
      'fa-steam-square',
      'fa-step-backward',
      'fa-step-forward',
      'fa-stethoscope',
      'fa-stop',
      'fa-street-view',
      'fa-strikethrough',
      'fa-stumbleupon',
      'fa-stumbleupon-circle',
      'fa-subscript',
      'fa-subway',
      'fa-suitcase',
      'fa-sun-o',
      'fa-superscript',
      'fa-support(alias)',
      'fa-table',
      'fa-tablet',
      'fa-tachometer',
      'fa-tag',
      'fa-tags',
      'fa-tasks',
      'fa-taxi',
      'fa-tencent-weibo',
      'fa-terminal',
      'fa-text-height',
      'fa-text-width',
      'fa-th',
      'fa-th-large',
      'fa-th-list',
      'fa-thumb-tack',
      'fa-thumbs-down',
      'fa-thumbs-o-down',
      'fa-thumbs-o-up',
      'fa-thumbs-up',
      'fa-ticket',
      'fa-times',
      'fa-times-circle',
      'fa-times-circle-o',
      'fa-tint',
      'fa-toggle-down(alias)',
      'fa-toggle-left(alias)',
      'fa-toggle-off',
      'fa-toggle-on',
      'fa-toggle-right(alias)',
      'fa-toggle-up(alias)',
      'fa-train',
      'fa-transgender',
      'fa-transgender-alt',
      'fa-trash',
      'fa-trash-o',
      'fa-tree',
      'fa-trello',
      'fa-trophy',
      'fa-truck',
      'fa-try',
      'fa-tty',
      'fa-tumblr',
      'fa-tumblr-square',
      'fa-turkish-lira(alias)',
      'fa-twitch',
      'fa-twitter',
      'fa-twitter-square',
      'fa-umbrella',
      'fa-underline',
      'fa-undo',
      'fa-university',
      'fa-unlink(alias)',
      'fa-unlock',
      'fa-unlock-alt',
      'fa-unsorted(alias)',
      'fa-upload',
      'fa-usd',
      'fa-user',
      'fa-user-md',
      'fa-user-plus',
      'fa-user-secret',
      'fa-user-times',
      'fa-users',
      'fa-venus',
      'fa-venus-double',
      'fa-venus-mars',
      'fa-viacoin',
      'fa-video-camera',
      'fa-vimeo-square',
      'fa-vine',
      'fa-vk',
      'fa-volume-down',
      'fa-volume-off',
      'fa-volume-up',
      'fa-warning(alias)',
      'fa-wechat(alias)',
      'fa-weibo',
      'fa-weixin',
      'fa-whatsapp',
      'fa-wheelchair',
      'fa-wifi',
      'fa-windows',
      'fa-won(alias)',
      'fa-wordpress',
      'fa-wrench',
      'fa-xing',
      'fa-xing-square',
      'fa-yahoo',
      'fa-yelp',
      'fa-yen(alias)',
      'fa-youtube',
      'fa-youtube-play',
      'fa-youtube-square',
    ];
  },
  extractBootstrapColsClasses: function (element) {
    var currClasses = jQuery.map(jQuery(element).attr('class').split(' '), jQuery.trim),
      newClasses = [];
    for (var i = 0; i < currClasses.length; i++) {
      if (currClasses[i] == 'col' || currClasses[i].match(/col\-\w{2}\-\d{1,2}/)) {
        newClasses.push(currClasses[i]);
      }
    }
    return newClasses;
  },
  initBadgesLibWnd: function (tableColumn) {
    var self = this;
    this.badgesLibWnd = jQuery('#ptsBadgesLibWnd').dialog({
      resizable: false,
      closeText: '',
      height: 'auto',
      width: '90%',
      title: 'BADGES LIBRARY',
      modal: true,
    });
    this.badgesLibWnd.find('.ptsBadgesLibSaveBtn').click(function () {
      badgeData = self.getBadgesData();
      self.badgesLibWndElement._setBadge(badgeData);
      ptsUtils.badgesLibWnd.dialog('close');
      return false;
    });
    this.badgesLibWnd.find('input[name=badge_name]').change(function () {
      self.updateBadgePrevLib();
    });

    var colorInputs = [{ key: 'badge_bg_color' }, { key: 'badge_txt_color' }],
      inpSelector,
      oneColorPickerOpt = jQuery.extend(g_ptsVandColorPickerOptions, {
        altField: null,
        position: { my: 'center top', at: 'right bottom', of: null },
        ok: function (event, cpColor) {
          self.updateBadgePrevLib();
        },
      });
    for (var i = 0; i < colorInputs.length; i++) {
      inpSelector = '.ptsColorPickInput[name="' + colorInputs[i].key + '"]';
      var colorInp = this.badgesLibWnd.find(inpSelector);
      oneColorPickerOpt.altField = inpSelector + ' + .ptsColorPickInputTear';
      oneColorPickerOpt.position.of = inpSelector + ' + .ptsColorPickInputTear';
      colorInp.colorpicker(oneColorPickerOpt);
    }
    this.fillInBadgeSettings(tableColumn);
    this.badgesLibWnd.find('.ptsTableBadgePosition').click(function () {
      self.badgesLibWnd.find('.ptsTableBadgePosition').removeClass('active');
      jQuery(this).addClass('active');
      self.badgesLibWnd.find('input[name=badge_pos]').val(jQuery(this).data('pos'));
      self.updateBadgePrevLib();
    });
  },
  fillInBadgeSettings: function (tableColumn) {
    // init color picker
    var $form = jQuery('#ptsBadgesLibForm'),
      backgroundColor = '#444444',
      foregroundColor = '#ffffff';

    if (tableColumn) {
      var $badge = tableColumn._$.find('.ptsColBadgeContent');
      // badge position
      var pos = tableColumn._$.attr('data-badge-badge_pos');
      if (pos) {
        this.badgesLibWnd.find('input[name=badge_pos]').val(pos);
      } else {
        this.badgesLibWnd.find('input[name=badge_pos]').val('left');
      }
      // badgeName
      if ($badge.length && tableColumn._$.attr('data-badge-badge_name') && tableColumn._$.attr('data-badge-badge_name') != '') {
        this.badgesLibWnd.find('input[name=badge_name]').val(tableColumn._$.attr('data-badge-badge_name'));
      } else {
        this.badgesLibWnd.find('input[name=badge_name]').val('SALE!');
      }

      if ($badge.length && $badge.eq(0).visible()) {
        // backgroundColor = $badge.css('background-color');
        // foregroundColor = $badge.css('color');
      }
    }
    // $form.find('input[name="badge_bg_color"]').css('background-color', backgroundColor);
    // $form.find('input[name="badge_txt_color"]').css('background-color', foregroundColor);
    var colorInputs = [
      // 	{key: 'badge_bg_color', def: backgroundColor}
      // ,	{key: 'badge_txt_color', def: foregroundColor}
    ];
    for (var i = 0; i < colorInputs.length; i++) {
      this.badgesLibWnd.find('.ptsColorPickInput[name=' + colorInputs[i].key + ']').colorpicker('setColor', colorInputs[i].def);
    }
  },
  showBadgesLibWnd: function (element) {
    if (!this.badgesLibWnd) {
      this.initBadgesLibWnd(element);
    } else {
      this.fillInBadgeSettings(element);
    }
    if (this.colorPicker) {
      this.colorPicker.startRender();
    }
    this.badgesLibWndElement = element;
    this.fillInBadgeLibData(this.getBadgesData());
    this.badgesLibWnd.dialog('open');
    var self = this;
    setTimeout(function () {
      self.updateBadgePrevLib();
    }, 500); // 500 is for transition for popup show
  },
  fillInBadgeLibData: function (data) {
    if (data.badge_name) {
      this.badgesLibWnd.find('input[name=badge_name]').val(data.badge_name);
    }
    if (data.badge_bg_color) {
      this.badgesLibWnd.find('.ptsColorPickInput[name=badge_bg_color]').colorpicker('setColor', data.badge_bg_color);
    }
    if (data.badge_txt_color) {
      this.badgesLibWnd.find('.ptsColorPickInput[name=badge_txt_color]').colorpicker('setColor', data.badge_txt_color);
    }
    if (data.badge_pos) {
      this.badgesLibWnd.find('.ptsTableBadgePosition[data-pos="' + data.badge_pos + '"]').click();
    }
  },
  updateBadgePrevLib: function ($badge, data) {
    $badge = $badge ? $badge : jQuery('#ptsTableBadgePrev');
    data = data ? data : this.getBadgesData();
    var $prevContent = $badge.find('.ptsColBadgeContent');
    $badge.attr({
      class: 'ptsColBadge ptsColBadge-' + data.badge_pos,
      style: '',
    });

    $prevContent
      .html(data.badge_name)
      .attr({
        style: '',
      })
      .css({
        // 	'background-color': data.badge_bg_color
        // ,	'color': data.badge_txt_color
        width: 'auto',
        display: 'inline-block',
      });
    var contW = $prevContent.outerWidth(),
      contH = $prevContent.outerHeight(),
      w = $badge.outerWidth(),
      h = $badge.outerHeight(),
      // We need to save as many attributes for frontend as possible - to not allow user theme styles broke our table
      fontSize = $prevContent.css('font-size'), // TODO: Add possibility to select custom font sizes
      lineHeight = $prevContent.css('line-height'),
      contAfterStyles = {
        display: 'block',
        'font-size': fontSize,
        'line-height': lineHeight,
      },
      afterStyles = {},
      newContentWidth = $prevContent.width();
    switch (data.badge_pos) {
      case 'top':
        contAfterStyles.width = contW;
        break;
      case 'right':
      case 'left':
        afterStyles[data.badge_pos] = 0;
        afterStyles.top = 0;
        afterStyles.width = contH;
        afterStyles.height = contW;
        contAfterStyles.width = 'auto';
        contAfterStyles.position = 'absolute';
        contAfterStyles.top = contW;
        contAfterStyles[data.badge_pos] = 0;
        break;
      case 'left-top':
      case 'right-top':
        var posKey = data.badge_pos === 'left-top' ? 'left' : 'right';
        afterStyles[posKey] = 0;
        afterStyles.top = 0;

        contAfterStyles.position = 'absolute';
        var coefOfDisplacement = 50;
        newContentWidth = contW + coefOfDisplacement;
        var d = 5,
          hipoten = newContentWidth / 2,
          catet = Math.sqrt((hipoten * hipoten) / 2);
        contAfterStyles.top = catet - Math.sqrt((contH * contH) / 2) - d;
        contAfterStyles[posKey] = -1 * (hipoten - catet) - d;
        $prevContent.width(newContentWidth);
        afterStyles.width = $prevContent.width();
        afterStyles.height = $prevContent.width();
        break;
    }
    $prevContent.css(contAfterStyles);
    $badge.css(afterStyles);
    var baseStyles = $prevContent.attr('style');
    var styleColor = 'color:' + data.badge_txt_color;
    var styleBg = 'background-color:' + data.badge_bg_color;
    var finalStyle = baseStyles + styleColor + ';' + styleBg + ';';
    jQuery($prevContent).attr('style', finalStyle);
  },
  getBadgesData: function () {
    var data = this.badgesLibWnd.find('#ptsBadgesLibForm').serializeAssoc();
    return data;
  },
};
//UTILS END
//BLOCKS FABRIC
/**
 * Blocks fabric - main object for whole blocks manipulations
 */
ptsBlockFabric.prototype.checkSortStart = function (ui) {
  if (!this._isSorting) {
    this._sortStart(ui);
    this._isSorting = true;
  }
};
ptsBlockFabric.prototype._sortStart = function (ui) {
  if (this._blocks.length) {
    var height = 178,
      margin = 20,
      draggedId = ui.item.attr('id'),
      elementFound = false,
      canvaPaddTop = 0,
      canvaPaddBottom = 0,
      currentScroll = jQuery(document).scrollTop(),
      newDocScroll = currentScroll,
      totalHeight = 0;
    for (var i = 0; i < this._blocks.length; i++) {
      var rawJq = this._blocks[i].getRaw(),
        originalHeight = rawJq.height();
      height = originalHeight * 0.5;
      if (height > 178) height = 178;
      rawJq
        .addClass('ptsInSortProcess')
        .data('original-height', originalHeight)
        .animate(
          {
            height: height + 'px',
            'margin-top': margin + 'px',
          },
          this._animationSpeed,
          function () {
            /*console.time('sortable - refreshPositions');
          jQuery('#ptsCanvas').sortable('refreshPositions');
          console.timeEnd('sortable - refreshPositions');*/
          }
        )
        .find('.ptsBlockContent')
        .zoom(0.5, 'center top');

      if (rawJq.attr('id') == draggedId) {
        elementFound = true;
      }
      var newFullHeight = height + margin;
      elementFound ? (canvaPaddBottom += originalHeight - newFullHeight) : (canvaPaddTop += originalHeight - newFullHeight);
      if (!draggedId && currentScroll && currentScroll >= totalHeight) {
        newDocScroll -= originalHeight - newFullHeight;
      }
      totalHeight += originalHeight;
    }
    setTimeout(function () {
      jQuery('#ptsCanvas').sortable('refreshPositions');
    }, this._animationSpeed);
    // if(draggedId) {
    // 	jQuery('#ptsCanvas').css({
    // 		'padding-top': canvaPaddTop
    // 	,	'padding-bottom': canvaPaddBottom
    // 	});
    // } else {
    // 	if(currentScroll) {
    // 		if(newDocScroll < 0)
    // 			newDocScroll = 0;
    // 		jQuery(document).scrollTop( newDocScroll );
    // 	}
    // }
  }
};
ptsBlockFabric.prototype.checkSortStop = function (ui) {
  if (this._isSorting) {
    this._sortStop(ui);
    this._isSorting = false;
  }
};
ptsBlockFabric.prototype._sortStop = function (ui) {
  if (this._blocks.length) {
    var height = 178,
      margin = 20,
      draggedId = ui.item.attr('id'),
      newDocScroll = 0,
      //,	scrollToIter = 0
      scrolledBlockPass = false,
      currentScroll = jQuery(document).scrollTop(),
      totalHeight = 0,
      offsetTop = ui.offset.top + ui.placeholder.height();
    for (var i = 0; i < this._blocks.length; i++) {
      var rawJq = this._blocks[i].getRaw(),
        originalHeight = rawJq.data('original-height');
      height = rawJq.height();
      rawJq
        .removeClass('ptsInSortProcess')
        .animate(
          {
            height: originalHeight,
            'margin-top': '0',
          },
          this._animationSpeed
        )
        .find('.ptsBlockContent')
        .zoom(1);

      if (draggedId && !scrolledBlockPass) {
        newDocScroll += originalHeight;
      }
      if (draggedId && rawJq.attr('id') == draggedId) {
        scrolledBlockPass = true;
      }

      if (!draggedId && totalHeight <= offsetTop) {
        newDocScroll += originalHeight;
      }
      totalHeight += height + margin;
    }
    jQuery('#ptsCanvas').css({
      'padding-top': 0,
      'padding-bottom': 0,
    });
    jQuery(document).scrollTop(newDocScroll);
  }
};
ptsBlockFabric.prototype.getDataForSave = function () {
  var res = [];
  if (this._blocks.length) {
    var prevDocScroll = jQuery(document).scrollTop();
    this.updateSortOrder();
    var requiredKeys = ['id', 'params', 'sort_order', 'original_id'];
    for (var i = 0; i < this._blocks.length; i++) {
      var requiredParams = {};
      for (var j = 0; j < requiredKeys.length; j++) {
        requiredParams[requiredKeys[j]] = this._blocks[i].get(requiredKeys[j]);
      }
      this._blocks[i].beforeSave();
      requiredParams.html = this._blocks[i].getHtml();
      requiredParams.css = this._blocks[i].get('css');
      this._blocks[i].afterSave();
      res.push(requiredParams);
    }
    // jQuery(document).scrollTop( prevDocScroll );
  }
  return res;
};
ptsBlockFabric.prototype.updateSortOrder = function () {
  if (this._blocks.length) {
    for (var i = 0; i < this._blocks.length; i++) {
      this._blocks[i].set('sort_order', this._blocks[i].getRaw().index());
    }
  }
};
ptsBlockFabric.prototype.getBlocks = function () {
  return this._blocks;
};
ptsBlockFabric.prototype.removeBlockByIter = function (iter) {
  if (this._blocks.length && this._blocks[iter]) {
    this._blocks.splice(iter, 1);
    if (this._blocks.length) {
      // Update iterators for blocks
      for (var i = 0; i < this._blocks.length; i++) {
        this._blocks[i].setIter(i);
      }
    }
  }
};
ptsBlockFabric.prototype.beforeSave = function () {
  if (this._blocks.length) {
    for (var i = 0; i < this._blocks.length; i++) {
      this._blocks[i].beforeSave();
    }
  }
};
ptsBlockFabric.prototype.afterSave = function () {
  if (this._blocks.length) {
    for (var i = 0; i < this._blocks.length; i++) {
      this._blocks[i].afterSave();
    }
  }
};
//BLOCKS FABRIC END
//EDITOR ELEMENTS
/**
 * Destroy current element
 */
ptsElementBase.prototype.destroy = function (clb) {
  if (this._$) {
    var childElements = this._getChildElements();
    if (childElements) {
      for (var i = 0; i < childElements.length; i++) {
        childElements[i]._remove();
      }
    }
    var self = this;
    this._$.slideUp(this._animationSpeed, function () {
      self._remove();
      if (clb && typeof clb === 'function') {
        clb();
      }
      if (g_ptsAllowAddUndo) {
        _ptsSaveCanvas();
      }
    });
  }
};
ptsElementBase.prototype.getMenuShowEvent = function () {
  return this._showMenuEvent;
};
ptsElementBase.prototype._remove = function () {
  if (this._showMenuEvent === 'click') {
    jQuery(document).unbind('click.menu_el_click_hide_' + this.getId());
  }
  this._destroyMenu();
  if (this._$) {
    this._$.remove();
    this._$ = null;
  }
  this._afterDestroy();
  this._block.removeElementByIterNum(this.getIterNum());
};
ptsElementBase.prototype._getChildElements = function () {
  var allFoundHtml = this._$.find('.ptsEl');
  if (allFoundHtml && allFoundHtml.length) {
    var foundElements = [],
      selfBlock = this.getBlock();
    allFoundHtml.each(function () {
      var element = selfBlock.getElementByIterNum(jQuery(this).data('iter-num'));
      if (element) {
        foundElements.push(element);
      }
    });
    return foundElements.length ? foundElements : false;
  }
  return false;
};
ptsElementBase.prototype._afterDestroy = function () {};
ptsElementBase.prototype.beforeSave = function () {
  this._destroyMoveHandler();
};
ptsElementBase.prototype.afterSave = function () {
  this._initMoveHandler();
};
ptsElementBase.prototype._initMenu = function () {
  if (this._menuOriginalId && this._menuOriginalId != '') {
    this._initMenuClbs();
    var menuParams = {
      changeable: this._changeable,
      showEvent: 'click',
    };
    this._menu = new window[this._menuClass](this._menuOriginalId, this, this._menuClbs, menuParams);
    if (!this._initedComplete) {
      var self = this;
      this._$.click(function (e) {
        e.stopPropagation();
        self.showMenu();
      });
      var startHideTimer = function () {
        jQuery(self._$).data(
          'hide-menu-timeout',
          setTimeout(function () {
            var scrollY = window.scrollY;
            self.hideMenu();
            window.scrollTo(0, scrollY);
          }, 500)
        );
      };
      this._$.hover(
        function () {
          clearTimeout(jQuery(self._$).data('hide-menu-timeout'));
        },
        function () {
          startHideTimer();
        }
      );
      this._menu.$().hover(
        function () {
          clearTimeout(jQuery(self._$).data('hide-menu-timeout'));
        },
        function () {
          startHideTimer();
        }
      );
      jQuery(document).on('click.menu_el_click_hide_' + this.getId(), function (e) {
        var $target = jQuery(e.target);
        if (self._menu && self._menu.isVisible() && !$target.closest(self._$).length && !$target.closest(self._menu.$()).length) {
          clearTimeout(jQuery(self._$).data('hide-menu-timeout'));
          self.hideMenu();
        }
      });
    }
    if (this._isMovable) {
      this._initMoveHandler();
      this._initMovableMenu();
    }
    this.initPostLinks(this._menu._$);
  } else {
  }
};
ptsElementBase.prototype.initPostLinks = function ($menu) {
  if (!this.includePostLinks) return;

  var $linkTab = $menu.find('.ptsPostLinkList'),
    $field = null,
    fieldSelector = $linkTab.attr('data-postlink-to');

  if (!fieldSelector.length) return;

  if (fieldSelector.indexOf(':parent') == 0) {
    fieldSelector = fieldSelector.substring(7, fieldSelector.length).trim();

    $field = $linkTab.parent().find(fieldSelector);
  } else {
    $field = jQuery(fieldSelector);
  }

  if (!$field.length) return;

  this.showPostsLinks($linkTab);

  $linkTab.css({
    height: 120,
  });

  $linkTab.on('click', 'li', function () {
    var $item = jQuery(this),
      url = $item.attr('data-value');

    if (!url) return;

    $field.val(url);

    $field.change();
  });

  $linkTab.slimScroll({
    height: 120,
    railVisible: true,
    alwaysVisible: true,
    allowPageScroll: true,
    color: '#f72497',
    opacity: 1,
    distance: 0,
    borderRadius: '3px',
  });

  $linkTab.parent('.slimScrollDiv').addClass('ptsPostLinkRoot').hide();

  var $rootTab = $linkTab.parent('.ptsPostLinkRoot');

  /** Hide and show handlers **/
  var ignoreHide = false,
    isFocus = false;

  $field.on('postlink.hide', function () {
    $rootTab.hide();

    $linkTab.hide();

    $field.trigger('postlink.hide:after');
  });

  $field.focus(function () {
    $field.trigger('postlink.show');

    $rootTab.show();

    $linkTab.show();

    isFocus = true;

    $field.trigger('postlink.show:after');
  });

  $rootTab.hover(
    function () {
      ignoreHide = true;
    },
    function () {
      ignoreHide = false;

      if (!isFocus) {
        $field.trigger('postlink.hide');
      }
    }
  );

  $field.blur(function () {
    isFocus = false;

    if (!ignoreHide) {
      $field.trigger('postlink.hide');
    }
  });
};
ptsElementBase.prototype.escapeString = function (str) {
  return jQuery('<div/>').text(str).html();
};
ptsElementBase.prototype.showPostsLinks = function ($tab) {
  if (!$tab.find('ul').length) {
    $tab.html('<ul></ul>');
  }

  $tab.find('ul').html('');

  for (var i in ptsEditor.posts) {
    $tab.find('ul').append('<li data-value="' + this.escapeString(ptsEditor.posts[i].url) + '">' + '<span>' + this.escapeString(ptsEditor.posts[i].title) + '</span>' + '</li>');
  }
};
ptsElementBase.prototype._closeMenuOnDocClick = function (e, element) {
  if (!this._menu.isVisible()) return;
  var $target = jQuery(e.target);
  if (!this.$().find($target).length && !this.getMenu().$().find($target).length) {
    this.hideMenu();
  }
};
ptsElementBase.prototype.getMenu = function () {
  return this._menu;
};
ptsElementBase.prototype._initMovableMenu = function () {
  this._menu.setMovable(true);
  this._menu
    .$()
    .bind('ptsElMenuReposite', function (e, menu, top, left, useAnimation, setActive) {
      var element = menu.getElement(),
        $element = element.$(),
        $menu = menu.$(),
        elWidth = $element.width(),
        menuWidth = $menu.width(),
        menuHeight = $menu.height();
      // var placePos = menu.$().find('.ptsElMenuMoveHandlerPlace').position()
      // ,	moveTop = -1 * menuHeight + placePos.top;
      // if($element.hasClass('hover')) {
      // 	moveTop -= g_ptsHoverMargin;
      // }

      // var elementParams = {
      // 	'top': moveTop
      // ,	'left': ((elWidth - menuWidth) / 2) + placePos.left - 10
      // };
      var elementParams = {
        top: '0',
        left: '-20px',
        //,	'background-color': '#f1f1f1'
      };

      if (typeof useAnimation != 'undefined' && useAnimation == true) {
        element._moveHandler.animate(elementParams, menu._animationSpeed);
      } else {
        element._moveHandler.css(elementParams);
      }
      if (typeof setActive == 'undefined' || setActive == true) {
        element._moveHandler.addClass('active');
      }
    })
    .bind('ptsElMenuHide', function (e, menu) {
      var element = menu.getElement();
      if (!element._sortInProgress) {
        element._moveHandler.removeClass('active');
      }
    });
};
ptsElementBase.prototype.onSortStart = function (axis) {
  this._sortInProgress = true;
  this._moveHandler.addClass('sortInProgress');
  if (axis === 'y') {
    this._$.addClass('ptsColSortInProgressY');
  }
  this._menu.hide();
};
ptsElementBase.prototype.onSortStop = function () {
  this._sortInProgress = false;
  this._moveHandler.removeClass('sortInProgress');
  this._$.removeClass('ptsColSortInProgressY');
  this._menu.show();
};
ptsElementBase.prototype._initMenuClbs = function () {
  var self = this;
  this._menuClbs['.ptsRemoveElBtn'] = function () {
    self.destroy();
    setTimeout(function () {
      _ptsAddUndoBuffer(true);
    }, 300);
  };
  if (this._changeable) {
    this._menuClbs['.ptsTypeTxtBtn'] = function () {
      self.getBlock().replaceElement(self, 'txt_item_html', 'txt');
    };
    this._menuClbs['.ptsTypeImgBtn'] = function () {
      self.getBlock().replaceElement(self, 'img_item_html', 'img');
    };
    this._menuClbs['.ptsTypeIconBtn'] = function () {
      self.getBlock().replaceElement(self, 'icon_item_html', 'icon');
    };
    this._menuClbs['.ptsTypeButtonBtn'] = function () {
      self.getBlock().replaceElement(self, 'icon_item_html', 'btn');
    };
  }
};
ptsElementBase.prototype._initMoveHandler = function () {
  if (this._isMovable && !this._moveHandler) {
    var handler = this._$.find('.ptsMoveHandler');
    this._moveHandler = handler.length ? handler : jQuery('#ptsMoveHandlerExl').clone().removeAttr('id').appendTo(this._$);
  }
};
ptsElementBase.prototype._destroyMoveHandler = function () {
  if (this._isMovable) {
    this._moveHandler.remove();
    this._moveHandler = null;
  }
};
ptsElementBase.prototype._afterFullContentLoad = function () {
  //sthis.repositeMenu();
};
ptsElementBase.prototype._destroyMenu = function () {
  if (this._menu) {
    this._menu.destroy();
    this._menu = null;
  }
};
ptsElementBase.prototype.showMenu = function () {
  if (this._menu) {
    this._menu.show();
  }
};
ptsElementBase.prototype.hideMenu = function () {
  if (this._menu) {
    this._menu.hide();
  }
};
ptsElementBase.prototype.menuInAnimation = function () {
  if (this._menu) {
    return this._menu.inAnimation();
  }
  return false;
};
ptsElementBase.prototype.setMovable = function (state) {
  this._isMovable = state;
};
ptsElementBase.prototype.repositeMenu = function () {
  if (this._menu) {
    this._menu.reposite();
  }
};
/**
 * Text element
 */
function ptsElement_txt(jqueryHtml, block) {
  this._elId = null;
  this._editorElement = null;
  this._editor = null;
  this.includePostLinks = true;
  this._editorToolbarBtns = [['pts_editattrs'], ['pts_fontselect'], ['pts_fontsizeselect'], ['pts_code', 'bold', 'italic', 'strikethrough'], ['pts_link'], ['pts_tooltip'], ['forecolor']];
  ptsElement_txt.superclass.constructor.apply(this, arguments);
}
extendPts(ptsElement_txt, ptsElementBase);
ptsElement_txt.prototype._init = function () {
  ptsElement_txt.superclass._init.apply(this, arguments);
  var id = this._$.attr('id'),
    self = this;
  if (!id || id == '') {
    this._$.attr('id', 'ptsTxt_' + mtRand(1, 99999));
  }
  var toolbarBtns = [];
  for (var i = 0; i < this._editorToolbarBtns.length; i++) {
    toolbarBtns.push(typeof this._editorToolbarBtns[i] === 'string' ? this._editorToolbarBtns[i] : this._editorToolbarBtns[i].join(' '));
  }
  if (typeof ptsMCEUrl != 'undefined') {
    tinyMCE.baseURL = ptsMCEUrl;
  }
  tinyMCE.suffix = '.min';
  this._editorElement = this._$.tinymce({
    inline: true,
    toolbar: toolbarBtns.join(' |  | '),
    menubar: false,
    autofocus: false,
    plugins: 'pts_editattrs pts_textcolor pts_link pts_fontselect pts_fontsizeselect pts_tooltip pts_code',
    fontsize_formats: '8pt 10pt 12pt 14pt 18pt 24pt 36pt 48pt 64pt 72pt',
    skin: 'octo',
    convert_urls: false,
    link_context_toolbar: true,
    setup: function (ed) {
      this._editor = ed;
      ed.on('keyup', function (e) {
        var $cell = self._$.closest('.ptsCell, .ptsColDesc, .ptsColHeader');
        if ($cell.length) {
          var scrollTop = jQuery(window).scrollTop();
          $cell.css('height', 'auto');
          var newHeight = $cell[0].scrollHeight;
          $cell.css('height', newHeight + 'px');
          jQuery(window).scrollTop(scrollTop);
        }
        var selectionCoords = getSelectionCoords();
        ptsMceMoveToolbar(self._editorElement.tinymce(), selectionCoords.x);
        self.getBlock().hideElementsMenus();
      });
      ed.on('blur', function (e) {
        var currentContent = ed.getContent();
        if (e.target._ptsChanged) {
          e.target._ptsChanged = false;
          var scrollTop = jQuery(window).scrollTop();
          self.getBlock().contentChanged();
          jQuery(window).scrollTop(scrollTop);
          _ptsSaveCanvas();
        }
        jQuery('.mce-container.mce-panel.mce-floatpanel.mce-menu:visible').hide();
      });
      ed.on('click', function (e) {
        ptsMceMoveToolbar(self._editorElement.tinymce(), e.clientX);
        self.getBlock().hideElementsMenus();
        if (ed.theme.panel.hasOwnProperty('isInitPostlinkClick')) return;
        var handler = function () {
          ed.theme.panel.isInitPostlinkClick = true;
          if (!self || !self._$) return;
          var $fieldWp = jQuery('#' + self._$.attr('id') + 'ptsPostLinkList');
          if ($fieldWp.length) {
            ed.theme.panel.off('click', handler);
            self.initPostLinks($fieldWp.parents('.mce-container'));
          }
        };
        ed.theme.panel.on('click', handler);
      });
      if (self._afterEditorInit) {
        self._afterEditorInit(ed);
      }
    },
  });
  this._$.removeClass('mce-edit-focus');
  this._$.bind('dragover drop', function (event) {
    event.preventDefault();
  });
};
ptsElement_txt.prototype.destroy = function (clb) {
  if (this._editor) {
    this._editor.remove(); // Clean up TinyMCE editor
  }
  ptsElement_txt.superclass.destroy.apply(this, arguments);
};
ptsElement_txt.prototype.getEditorElement = function () {
  return this._editorElement;
};
ptsElement_txt.prototype.getEditor = function () {
  return this._editor;
};
ptsElement_txt.prototype.beforeSave = function () {
  ptsElement_txt.superclass.beforeSave.apply(this, arguments);
  if (!this._$) return; // TODO: Make this work corect - if there are no html (_$) - then this method should not simple triggger. For now - it trigger even if _$ === null
  this._elId = this._$.attr('id');
  this._$.removeAttr('id').removeAttr('contenteditable').removeAttr('spellcheck').removeClass('mce-content-body mce-edit-focus');
};
ptsElement_txt.prototype.afterSave = function () {
  ptsElement_txt.superclass.afterSave.apply(this, arguments);
  if (this._elId) {
    this._$.attr('id', this._elId).attr('contenteditable', 'true').attr('spellcheck', 'false').addClass('mce-content-body');
  }
};
/**
 * Image element
 */
function ptsElement_img(jqueryHtml, block) {
  if (typeof this._menuOriginalId === 'undefined') {
    this._menuOriginalId = 'ptsElMenuTableCellImgExl';
  }
  this._menuClass = 'ptsElementMenu_img';
  this.includePostLinks = true;
  ptsElement_img.superclass.constructor.apply(this, arguments);
  var self = this;
  this._getImg().load(function () {
    self._block.contentChanged();
  });
}
extendPts(ptsElement_img, ptsElementBase);
ptsElement_img.prototype._beforeImgChange = function (opts, attach, imgUrl, imgToChange) {};
ptsElement_img.prototype._afterImgChange = function (opts, attach, imgUrl, imgToChange) {};
// ptsElement_img.prototype._init: Initialize image-specific behaviors
ptsElement_img.prototype._init = function () {
  ptsElement_img.superclass._init.apply(this, arguments); // Call parent init

  var self = this;
  var img = this._getImg(); // Retrieve the image element

  // Only enable resizable and related features in edit mode (backend)
  if (g_ptsEdit) {
    // Check if jQuery UI resizable is available
    if (!jQuery.fn.resizable) {
      console.warn('jQuery UI Resizable is not loaded. Image resizing will not work.');
      return;
    }

    // Function to apply/resync bindings (called on init and after toggle rebuild)
    function applyBindings() {
      // Get current img (in case HTML was rebuilt)
      img = self._getImg();

      // Clean up old resizable and events first
      if (jQuery(img).hasClass('ui-resizable')) {
        try {
          jQuery(img).resizable('destroy');
        } catch (e) {
          console.warn('Error destroying resizable:', e);
        }
      }
      jQuery(img).removeClass('ui-resizable ui-resizable-active');
      jQuery(img).find('.ui-resizable-handle').remove();
      if (jQuery(img).parent().hasClass('ui-wrapper')) {
        jQuery(img).unwrap();
      }
      jQuery(img).off('dblclick click'); // Remove old event handlers

      // Re-bind double-click to enable resizable
      jQuery(img).on('dblclick', function (e) {
        // Enable resizable
        if (!jQuery(this).hasClass('ui-resizable')) {
          jQuery(this).resizable({
            aspectRatio: true,
            handles: 'se',
            ghost: true,
            minWidth: 20,
            minHeight: 20,
            stop: function (event, ui) {
              self._block._refreshCellsHeight();
              self._block.contentChanged();
              _ptsSaveCanvas();
            },
          });
        }
        jQuery(this).addClass('ui-resizable-active');
        e.stopPropagation();
        e.preventDefault();
      });

      // Prevent single click bubbling
      jQuery(img).on('click', function (e) {
        // e.stopPropagation();
      });

      // Disable on click outside (stateful per element ID)
      jQuery(document)
        .off('click.ptsdblclick_' + self._id)
        .on('click.ptsdblclick_' + self._id, function (e) {
          if (!jQuery(e.target).closest(img).length && jQuery(img).hasClass('ui-resizable-active')) {
            if (jQuery(img).hasClass('ui-resizable')) {
              try {
                jQuery(img).resizable('destroy');
              } catch (e) {
                console.warn('Error destroying resizable:', e);
              }
            }
            jQuery(img).removeClass('ui-resizable-active');
            jQuery(img).find('.ui-resizable-handle').remove();
            if (jQuery(img).parent().hasClass('ui-wrapper')) {
              jQuery(img).unwrap();
            }
            if (self._$.parents('.ptsTog').length) {
              jQuery(document.body).trigger('updateToggleHtml');
            }
          }
        });

      // Reposition menu
      self.repositeMenu();
    }

    // Apply bindings initially
    applyBindings();

    // Re-apply bindings after toggle rebuild (bind to toggle-specific event)
    jQuery(document.body)
      .off('changeToggleReplaceHrefBtn.ptstoggle_' + self._id)
      .on('changeToggleReplaceHrefBtn.ptstoggle_' + self._id, function () {
        setTimeout(function () {
          applyBindings(); // Re-sync after toggle HTML change
          self._block.contentChanged(); // Optional: Trigger content change if needed
        }, 100); // Short delay to ensure DOM update complete
      });

    // // Menu on hover (if not already 'hover')
    // if (self._showMenuEvent !== 'click') {
    //     jQuery(self._$).hover(
    //         function() {
    //             self.showMenu();
    //         },
    //         function() {
    //             setTimeout(function() {
    //                 if (!self.menuInAnimation() && self._menu && !jQuery(self._menu.$()).is(':hover')) {
    //                     self.hideMenu();
    //                 }
    //             }, 1000);
    //         }
    //     );
    //     if (self._menu) {
    //         self._menu.$().hover(
    //             function() {
    //                 clearTimeout(jQuery(self._$).data('hide-menu-timeout'));
    //             },
    //             function() {
    //                 jQuery(self._$).data('hide-menu-timeout', setTimeout(function() {
    //                     self.hideMenu();
    //                 }, 1000));
    //             }
    //         );
    //     }
    // }
  }

  // Handle image load for menu positioning
  img.load(function () {
    self.repositeMenu();
  });

  // Fallback for already loaded images
  if (img[0]) {
    if (img[0].complete) {
      self.repositeMenu();
    }
  }
};

// ptsElement_img.prototype.beforeSave: Clean up resizable before saving
ptsElement_img.prototype.beforeSave = function () {
  ptsElement_img.superclass.beforeSave.apply(this, arguments);
  var img = this._getImg();
  if (jQuery(img).hasClass('ui-resizable')) {
    try {
      jQuery(img).resizable('destroy');
    } catch (e) {
      console.warn('Error destroying resizable in beforeSave:', e);
    }
    jQuery(img).find('.ui-resizable-handle').remove();
    if (jQuery(img).parent().hasClass('ui-wrapper')) {
      jQuery(img).unwrap();
    }
  }
  jQuery(img).removeClass('ui-resizable-active');
};

// Set default showMenuEvent for images to 'hover'
if (typeof ptsElement_img.prototype._showMenuEvent === 'undefined') {
  ptsElement_img.prototype._showMenuEvent = 'click';
}
ptsElement_img.prototype._initMenuClbs = function () {
  ptsElement_img.superclass._initMenuClbs.apply(this, arguments);
  var self = this;
  this._menuClbs['.ptsImgChangeBtn'] = function () {
    self.set('type', 'img');
    self._getImg().show();
    self._getVideoFrame().remove();
    ptsCallWpMedia({
      id: self._$.attr('id'),
      clb: function (opts, attach, imgUrl) {
        var imgToChange = self._getImg();
        self._block.beforeSave();
        self._innerImgsLoaded = 0;
        self._beforeImgChange(opts, attach, imgUrl, imgToChange);
        imgToChange.attr('src', imgUrl);
        self._afterImgChange(opts, attach, imgUrl, imgToChange);
        self._block.afterSave();
        self._block.contentChanged();
        _ptsSaveCanvas();
      },
    });
  };
  this._menuClbs['.ptsImgVideoSetBtn'] = function () {
    self.set('type', 'video');
    self._buildVideo(self._menu.$().find('[name=video_link]').val());
  };
};
ptsElement_img.prototype._buildVideo = function (url) {
  url = url ? jQuery.trim(url) : false;
  if (url) {
    var $editArea = this._getEditArea(),
      $videoFrame = this._getVideoFrame($editArea),
      $img = this._getImg($editArea),
      src = ptsUtils.urlToVideoSrc(url);
    $videoFrame
      .attr({
        src: src,
        width: $img.width(),
        height: $img.height(),
      })
      .show();
    $img.hide();
  }
};
ptsElement_img.prototype._getVideoFrame = function (editArea) {
  editArea = editArea ? editArea : this._getEditArea();
  var videoFrame = editArea.find('iframe.ptsVideo');
  if (!videoFrame.length) {
    videoFrame = jQuery('<iframe class="ptsVideo" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen />').appendTo(editArea);
  }
  return videoFrame;
};
ptsElement_img.prototype._getImg = function (editArea) {
  if (!this._$ || !this._$.length) {
    console.warn('ptsElement_img._getImg: Element is not initialized or removed from DOM');
    return jQuery();
  }
  return this._$.find('.ptsElArea img:first');
};
ptsElement_img.prototype._initMenu = function () {
  ptsElement_img.superclass._initMenu.apply(this, arguments);
  var self = this;
  this._menu
    .$()
    .find('[name=video_link]')
    .change(function () {
      self._buildVideo(jQuery(this).val());
    })
    .keyup(function (e) {
      if (e.keyCode == 13) {
        // Enter
        self._buildVideo(jQuery(this).val());
      }
    });
};
ptsElement_img.prototype._getLink = function () {
  var $link = this._$.find('a.ptsLink');
  return $link.length ? $link : false;
};
ptsElement_img.prototype._setLinkAttr = function (attr, val) {
  switch (attr) {
    case 'href':
      if (val) {
        var $link = this._createLink();
        $link.attr(attr, val);
      } else this._removeLink();
      break;
    case 'title':
      var $link = this._createLink();
      $link.attr(attr, val);
      break;
    case 'target':
      var $link = this._createLink();
      val ? $link.attr('target', '_blank') : $link.removeAttr('target');
      break;
  }
};
ptsElement_img.prototype._createLink = function () {
  var $link = this._getLink();
  if (!$link) {
    $link = jQuery('<a class="ptsLink" />').append(this._$.find('img')).appendTo(this._getEditArea());
    $link.click(function (e) {
      e.preventDefault();
    });
  }
  return $link;
};
ptsElement_img.prototype._removeLink = function () {
  var $link = this._getLink();
  if ($link) {
    this._getEditArea().append(this._$.find('img'));
    $link.remove();
  }
};
ptsElement_img.prototype._isRelNofollow = function (nofollow) {
  var $link = this._getLink();

  if (!$link) $link = this._createLink();

  if (nofollow) $link.attr('rel', 'nofollow');
  else $link.removeAttr('rel');
};
/**
 * Gallery image element
 */
function ptsElement_gal_img(jqueryHtml, block) {
  if (typeof this._menuOriginalId === 'undefined') {
    this._menuOriginalId = 'ptsElMenuGalItemExl';
  }
  ptsElement_gal_img.superclass.constructor.apply(this, arguments);
}
extendPts(ptsElement_gal_img, ptsElement_img);
ptsElement_gal_img.prototype._afterDestroy = function () {
  ptsElement_gal_img.superclass._afterDestroy.apply(this, arguments);
  this._block.recalcRows();
};
ptsElement_gal_img.prototype._afterImgChange = function (opts, attach, imgUrl, imgToChange) {
  ptsElement_gal_img.superclass._afterImgChange.apply(this, arguments);
  imgToChange.attr('data-full-img', attach.url);
  imgToChange.parents('.ptsGalLink:first').attr('href', attach.url);
};
/**
 * Menu item element
 */
function ptsElement_menu_item(jqueryHtml, block) {
  /*if(typeof(this._menuOriginalId) === 'undefined') {
    this._menuOriginalId = 'ptsElMenuGalItemExl';
  }*/
  ptsElement_menu_item.superclass.constructor.apply(this, arguments);
}
extendPts(ptsElement_menu_item, ptsElement_txt);
ptsElement_menu_item.prototype._afterEditorInit = function (editor) {
  var self = this;
  editor.addButton('tables_remove', {
    title: 'Remove',
    onclick: function (e) {
      self.destroy();
    },
  });
};
ptsElement_menu_item.prototype._beforeInit = function () {
  this._editorToolbarBtns.push('tables_remove');
};

/**
 * Menu item image
 */
function ptsElement_menu_item_img(jqueryHtml, block) {
  if (typeof this._menuOriginalId === 'undefined') {
    this._menuOriginalId = 'ptsElMenuMenuItemImgExl';
  }
  ptsElement_menu_item_img.superclass.constructor.apply(this, arguments);
}
extendPts(ptsElement_menu_item_img, ptsElement_img);
/**
 * Input item
 */
function ptsElement_input(jqueryHtml, block) {
  if (typeof this._menuOriginalId === 'undefined') {
    this._menuOriginalId = 'ptsElMenuInputExl';
  }
  ptsElement_input.superclass.constructor.apply(this, arguments);
}
extendPts(ptsElement_input, ptsElementBase);
ptsElement_input.prototype._init = function () {
  ptsElement_input.superclass._init.apply(this, arguments);
  var saveClb = function (element) {
    jQuery(element).attr('placeholder', jQuery(element).val());
    jQuery(element).val('');
    _ptsSaveCanvasDelay();
  };
  this._getInput()
    .focus(function () {
      jQuery(this).val(jQuery(this).attr('placeholder'));
    })
    .blur(function () {
      if (jQuery(this).data('saved')) {
        jQuery(this).data('saved', 0);
        return;
      }
      saveClb(this);
    })
    .keyup(function (e) {
      if (e.keyCode == 13) {
        // Enter
        saveClb(this);
        jQuery(this).data('saved', 1).trigger('blur'); // We must blur from element after each save in any case
      }
    });
};
ptsElement_input.prototype._getInput = function () {
  if (!this._$) return; // TODO: Make this work corect - if there are no html (_$) - then this method should not simple triggger. For now - it trigger even if _$ === null
  // TODO: Modify this to return all fields types
  return this._$.find('input');
};
ptsElement_input.prototype._initMenu = function () {
  ptsElement_input.superclass._initMenu.apply(this, arguments);
  if (!this._$) return; // TODO: Make this work corect - if there are no html (_$) - then this method should not simple triggger. For now - it trigger even if _$ === null
  var self = this,
    menuReqCheck = this._menu.$().find('[name="input_required"]');
  menuReqCheck.change(function () {
    var required = jQuery(this).attr('checked');
    if (required) {
      self._getInput().attr('required', '1');
    } else {
      self._getInput().removeAttr('required');
    }
    self._block.setFieldRequired(self._getInput().get(0).name, helperChecked ? 1 : 0);
    _ptsSaveCanvasDelay();
  });
  self._getInput().attr('required') ? menuReqCheck.attr('checked', 'checked') : menuReqCheck.removeAttr('checked');
};
ptsElement_input.prototype.destroy = function () {
  // Remove field from block fields list at first
  var name = this._getInput().attr('name');
  this._block.removeField(name);
  ptsElement_input.superclass.destroy.apply(this, arguments);
};
/**
 * Input button item
 */
function ptsElement_input_btn(jqueryHtml, block) {
  if (typeof this._menuOriginalId === 'undefined') {
    this._menuOriginalId = 'ptsElMenuInputBtnExl';
  }
  ptsElement_input_btn.superclass.constructor.apply(this, arguments);
}
extendPts(ptsElement_input_btn, ptsElementBase);
ptsElement_input_btn.prototype._getInput = function () {
  // TODO: Modify this to return all fields types
  return this._$.find('input');
};
ptsElement_input_btn.prototype._init = function () {
  ptsElement_input_btn.superclass._init.apply(this, arguments);
  var saveClb = function (element) {
    jQuery(element).attr('type', 'submit');
    _ptsSaveCanvasDelay();
  };
  this._getInput()
    .click(function () {
      return false;
    })
    .focus(function () {
      var value = jQuery(this).val();
      jQuery(this).attr('type', 'text').val(value);
    })
    .blur(function () {
      if (jQuery(this).data('saved')) {
        jQuery(this).data('saved', 0);
        return;
      }
      saveClb(this);
    })
    .keyup(function (e) {
      if (e.keyCode == 13) {
        // Enter
        saveClb(this);
        jQuery(this).data('saved', 1).trigger('blur'); // We must blur from element after each save in any case
      }
    });
};
/**
 * Standart button item
 */
ptsElement_btn.prototype.beforeSave = function () {
  ptsElement_btn.superclass.beforeSave.apply(this, arguments);
  this._getEditArea().removeAttr('contenteditable');
};
ptsElement_btn.prototype.afterSave = function () {
  ptsElement_btn.superclass.afterSave.apply(this, arguments);
  this._getEditArea().attr('contenteditable', true);
};
ptsElement_btn.prototype._init = function () {
  ptsElement_btn.superclass._init.apply(this, arguments);
  var self = this;
  this._getEditArea()
    .attr('contenteditable', true)
    .blur(function () {
      setTimeout(function () {
        self._block.contentChanged();
      }, 2000);
      //_ptsSaveCanvasDelay();
    })
    .keypress(function (e) {
      if (e.keyCode == 13 && window.getSelection) {
        // Enter
        document.execCommand('insertHTML', false, '<br>');
        if (typeof e.preventDefault != 'undefined') {
          e.preventDefault();
        } else {
          e.returnValue = false;
        }
      }
    });
  if (this.get('customhover-clb')) {
  }
};
ptsElement_btn.prototype._setColor = function (color) {
  this.set('bgcolor', color);
  var bgElements = this.get('bgcolor-elements');
  if (bgElements) bgElements = this._$.find(bgElements);
  else bgElements = this._$;
  switch (this.get('bgcolor-to')) {
    case 'border': // Change only borders color
      bgElements.css({
        'border-color': color,
      });
      break;
    case 'txt':
      bgElements.css({
        color: color,
      });
      break;
    case 'bg':
    default:
      bgElements.css({
        'background-color': color,
      });
      break;
  }
  if (this._haveAdditionBgEl === null) {
    this._haveAdditionBgEl = this._$.find('.ptsAddBgEl');
    if (!this._haveAdditionBgEl.length) {
      this._haveAdditionBgEl = false;
    }
  }
  if (this._haveAdditionBgEl) {
    this._haveAdditionBgEl.css({
      'background-color': color,
    });
  }
  if (this.get('bgcolor-clb')) {
    var clbName = this.get('bgcolor-clb');
    if (typeof this[clbName] === 'function') {
      this[clbName](color);
    }
  }
};
/**
 * Icon item
 */
function ptsElement_icon(jqueryHtml, block) {
  if (typeof this._menuOriginalId === 'undefined') {
    this._menuOriginalId = 'ptsElMenuIconExl';
  }
  this._menuClass = 'ptsElementMenu_icon';
  this.includePostLinks = true;
  ptsElement_icon.superclass.constructor.apply(this, arguments);
}
extendPts(ptsElement_icon, ptsElementBase);
ptsElement_icon.prototype._setColor = function (color) {
  this.set('color', color);
  this._getEditArea().css('color', color);
};
ptsElement_icon.prototype._getLink = function () {
  var $link = this._$.find('a.ptsLink');
  return $link.length ? $link : false;
};
ptsElement_icon.prototype._setLinkAttr = function (attr, val) {
  switch (attr) {
    case 'href':
      if (val) {
        var $link = this._createLink();
        $link.attr(attr, val);
      } else this._removeLink();
      break;
    case 'title':
      var $link = this._createLink();
      $link.attr(attr, val);
      break;
    case 'target':
      var $link = this._createLink();
      val ? $link.attr('target', '_blank') : $link.removeAttr('target');
      break;
  }
};
ptsElement_icon.prototype._createLink = function () {
  var $link = this._getLink();
  if (!$link) {
    $link = jQuery('<a class="ptsLink" />').append(this._$.find('.ptsInputShell')).appendTo(this._$);
    $link.click(function (e) {
      e.preventDefault();
    });
  }
  return $link;
};
ptsElement_icon.prototype._removeLink = function () {
  var $link = this._getLink();
  if ($link) {
    this._$.append($link.find('.ptsInputShell'));
    $link.remove();
  }
};
ptsElement_icon.prototype._isRelNofollow = function (nofollow) {
  var $link = this._getLink();

  if (!$link) $link = this._createLink();

  if (nofollow) $link.attr('rel', 'nofollow');
  else $link.removeAttr('rel');
};
/**
 * Table column element
 */
ptsElement_table_col.prototype._setColor = function (color) {
  if (color) {
    this.set('color', color);
  } else {
    color = this.get('color');
  }
  var enbColor = parseInt(this.get('enb-color')),
    block = this.getBlock(),
    colNum = this._colNum,
    cssTag = 'col color ' + colNum,
    cellColorCss = block.getParam('cell_color_css'),
    useCss = cellColorCss && cellColorCss !== '';
  if (enbColor) {
    if (useCss) {
      block.setTaggedStyle(block.getParam('cell_color_css'), cssTag, { num: colNum, color: color });
    } else {
      var $bgColorTo = this._$.find('[data-bg-to]'),
        firstBgColor = this.get('first-bg-color');
      if (!firstBgColor) {
        this.set('first-bg-color', $bgColorTo.css('background-color'));
      }
      $bgColorTo.css('background-color', color);
    }
  } else {
    if (useCss) {
      block.removeTaggedStyle(cssTag);
    } else {
      var $bgColorTo = this._$.find('[data-bg-to]');
      $bgColorTo.css('background-color', this.get('first-bg-color'));
    }
  }
  //_ptsSaveCanvas();
};
ptsElement_table_col.prototype._setColNum = function (num) {
  this._colNum = num;
};
ptsElement_table_col.prototype._afterDestroy = function () {
  ptsElement_table_col.superclass._afterDestroy.apply(this, arguments);
  this._block.checkColWidthPerc();
};
ptsElement_table_col.prototype._showSelectBadgeWnd = function () {
  this.hideMenu();
  ptsUtils.showBadgesLibWnd(this);
};
ptsElement_table_col.prototype._disableBadge = function () {
  this._getBadgeHtml().hide();
};
ptsElement_table_col.prototype._setBadge = function (data) {
  if (data) {
    for (var key in data) {
      this.set('badge-' + key, data[key]);
    }
  } else {
    data = this._getBadgeData();
  }
  if (!data) return;

  ptsUtils.updateBadgePrevLib(this._getBadgeHtml().show(), data);
  this.set('enb-badge', 1);
  var $enbBadgeCheck = this._menu.$().find('[name=enb_badge_col]');
  $enbBadgeCheck.prop('checked', true);
  $enbBadgeCheck.data('checked', true);
  $enbBadgeCheck.attr('checked', true);
};
ptsElement_table_col.prototype._getBadgeData = function () {
  var keys = ['badge_name', 'badge_bg_color', 'badge_txt_color', 'badge_pos'],
    data = {};
  for (var i = 0; i < keys.length; i++) {
    data[keys[i]] = this.get('badge-' + keys[i]);
    if (!data[keys[i]]) return false;
  }
  return data;
};
ptsElement_table_col.prototype._getBadgeHtml = function () {
  var $badge = this._$.find('.ptsColBadge');
  if (!$badge.length) {
    $badge = jQuery('<div class="ptsColBadge"><div class="ptsColBadgeContent"></div></div>').appendTo(this._getEditArea());
  }
  return $badge;
};
/**
 * Table description column element
 */
ptsElement_table_col_desc.prototype._initMenu = function () {
  ptsElement_table_col_desc.superclass._initMenu.apply(this, arguments);
  // Column description created from usual table column element, with it's menu.
  // But we can't move or remove (we can hide this from block settings) this type of column, so let's just remove it's move handle from menu.
  var $moveHandle = this._menu.$().find('.ptsElMenuMoveHandlerPlace'),
    $removeBtn = this._menu.$().find('.ptsRemoveElBtn');
  $moveHandle.next('.ptsElMenuBtnDelimiter').remove();
  $moveHandle.remove();
  $removeBtn.prev('.ptsElMenuBtnDelimiter').remove();
  $removeBtn.remove();
  this._menu.$().css('min-width', '130px');
};
/**
 * Table cell element
 */
function ptsElement_table_cell(jqueryHtml, block) {
  if (typeof this._menuOriginalId === 'undefined') {
    this._menuOriginalId = 'ptsElMenuTableCellExl';
  }
  this._menuClass = 'ptsElementMenu_table_cell';
  ptsElement_table_cell.superclass.constructor.apply(this, arguments);
}
extendPts(ptsElement_table_cell, ptsElementBase);
ptsElement_table_cell.prototype._initMenuClbs = function () {
  ptsElement_table_cell.superclass._initMenuClbs.apply(this, arguments);
  var self = this;
  this._menuClbs['.ptsTypeTxtBtn'] = function () {
    self._replaceElement('txt_cell_item', 'txt');
  };
  this._menuClbs['.ptsTypeImgBtn'] = function () {
    self._replaceElement('img_cell_item', 'img');
  };
  this._menuClbs['.ptsTypeIconBtn'] = function () {
    self._replaceElement('icon_cell_item', 'icon');
  };
  this._menuClbs['.ptsTypeButtonBtn'] = function () {
    self._replaceElement('icon_cell_item', 'btn');
  };
};
// ptsElement_table_cell.prototype._replaceElement = function(toParamCode, type) {
// 	var editArea = this._getEditArea()
// 	,	elementIter = editArea.find('.ptsEl').data('iter-num')
// 	,	block = this.getBlock();
// 	// Destroy current element in cell
// 	block.destroyElementByIterNum( elementIter );
// 	// Add new one
// 	editArea.html( block.getParam( toParamCode ) );
// 	block._initElementsForArea( editArea );
// 	this.set('type', type);
// 	this._menu.$().find('[name=type]').removeAttr('checked').filter('[value='+ type+ ']').attr('checked', 'checked');
// };
ptsElement_table_cell.prototype._replaceElement = function (toParamCode, type) {
  var editArea = this._getEditArea(),
    elementIter = editArea.find('.ptsEl').data('iter-num'), // Assume this gets the current content's iter-num; adjust if multiple .ptsEl
    block = this.getBlock(),
    self = this;

  // Destroy with callback to wait for animation/removal
  block.destroyElementByIterNum(elementIter, function () {
    // Extra safety: Clear the edit area (handles cases like .ptsTog with multiples)
    editArea.empty();

    // If .ptsTog exists (toggle enabled), recreate it or handle specially; assuming it's recreated in the param HTML
    editArea.html(block.getParam(toParamCode));

    // Re-init elements
    block._initElementsForArea(editArea);

    // Update type and menu
    self.set('type', type);
    self._menu
      .$()
      .find('[name=type]')
      .removeAttr('checked')
      .filter('[value=' + type + ']')
      .attr('checked', 'checked');

    // If toggle/schedule is enabled, re-apply duplication logic here if needed (e.g., call a toggle init function)
    // Example: if (parseInt(self.get('enb-toggle')) || parseInt(self.get('enb-schedule'))) { self._duplicateForToggle(); }
    // But implement _duplicateForToggle based on your toggle feature.

    // Trigger content change and save
    self.getBlock().contentChanged();
    _ptsSaveCanvas();
  });
};
/**
 * Table Cell Icon element
 */
function ptsElement_table_cell_icon(jqueryHtml, block) {
  if (typeof this._menuOriginalId === 'undefined') {
    this._menuOriginalId = 'ptsElMenuTableCellIconExl';
  }
  this._changeable = true;
  this.includePostLinks = true;
  ptsElement_table_cell_icon.superclass.constructor.apply(this, arguments);
}
extendPts(ptsElement_table_cell_icon, ptsElement_icon);
/**
 * Table Cell Image element
 */
function ptsElement_table_cell_img(jqueryHtml, block) {
  if (typeof this._menuOriginalId === 'undefined') {
    this._menuOriginalId = 'ptsElMenuTableCellImgExl';
  }
  this._changeable = true;
  this.includePostLinks = true;
  ptsElement_table_cell_img.superclass.constructor.apply(this, arguments);
}
extendPts(ptsElement_table_cell_img, ptsElement_img);
/**
 * Table Cell Image element
 */
function ptsElement_table_cell_txt(jqueryHtml, block) {
  this._typeBtns = {
    pts_el_menu_type_txt: {
      text: toeLangPts('Text'),
      type: 'txt',
      checked: true,
    },
    pts_el_menu_type_img: {
      text: toeLangPts('Image / Video'),
      type: 'img',
    },
    pts_el_menu_type_icon: {
      text: toeLangPts('Icon'),
      type: 'icon',
    },
    pts_el_menu_type_btn: {
      text: toeLangPts('Button'),
      type: 'btn',
    },
  };
  this.includePostLinks = true;
  ptsElement_table_cell_txt.superclass.constructor.apply(this, arguments);
}
extendPts(ptsElement_table_cell_txt, ptsElement_txt);
ptsElement_table_cell_txt.prototype._afterEditorInit = function (editor) {
  var self = this;

  var onclickClb = function () {
      var $btn = jQuery('#' + this._id).find('button:first'),
        $btnsGroupShell = $btn.parents('.mce-container.mce-btn-group:first'),
        $radio = $btn.find('input[type=radio]'),
        type = $radio.val();
      if (type === 'txt') return;

      $btnsGroupShell.find('input[type=radio]').removeAttr('checked');
      $radio.attr('checked') ? $radio.removeAttr('checked') : $radio.attr('checked', 'checked');
      // And now - let's make element change
      var element = this.settings._ptsElement;

      element.getBlock().replaceElement(element, type + '_item_html', type);
    },
    onPostRenderClb = function (type, checked) {
      var $btnShell = jQuery('#' + this._id),
        $btn = $btnShell.find('button:first'),
        txt = $btn.html(),
        $radioHtml = jQuery('<label><input type="radio" name="type" value="' + type + '" ' + (checked ? 'checked' : '') + ' />' + txt + '</label>');

      $btn.html('').append($radioHtml);
      $radioHtml.find('input').change(jQuery.proxy(onclickClb, this));
    };
  for (var btnKey in this._typeBtns) {
    editor.addButton(btnKey, {
      text: this._typeBtns[btnKey].text,
      _ptsType: this._typeBtns[btnKey].type,
      _ptsChecked: this._typeBtns[btnKey].checked,
      _ptsElement: this,
      classes: 'btn',
      onclick: function () {
        // see onPostRenderClb() - $radioHtml.find('input').change()
        jQuery.proxy(onclickClb, this)();
      },
      onpostrender: function (e) {
        jQuery.proxy(onPostRenderClb, this)(this.settings._ptsType, this.settings._ptsChecked);
      },
    });
  }

  editor.addButton('remove', {
    _ptsElement: this,
    icon: 'remove fa fa-trash-o',
    classes: 'btn',
    onclick: function () {
      self.destroy();
    },
  });
};
ptsElement_table_cell_txt.prototype._beforeInit = function () {
  var btnsPack = [];
  for (var btnKey in this._typeBtns) {
    btnsPack.push(btnKey);
  }
  btnsPack.push('remove');
  this._editorToolbarBtns.push(btnsPack);
};
//EDITOR ELEMENTS END
//EDITOR ELEMENTS MENU START
function ptsElementMenu(menuOriginalId, element, btnsClb, params) {
  params = params || {};
  this._$ = null;
  this._animationSpeed = g_ptsAnimationSpeed;
  this._menuOriginalId = menuOriginalId;
  this._element = element;
  this._btnsClb = btnsClb;
  this._visible = false;
  this._isMovable = false;
  this._changeable = params.changeable ? params.changeable : false;
  this._inAnimation = false;
  this._id = 'ptsElMenu_' + mtRand(1, 99999);
  this._showEvent = params.showEvent ? params.showEvent : 'click';
  this.init();
}
ptsElementMenu.prototype.getId = function () {
  return this._id;
};
ptsElementMenu.prototype.setMovable = function (state) {
  this._isMovable = state;
};
ptsElementMenu.prototype.setChangeable = function (state) {
  this._changeable = state;
};
ptsElementMenu.prototype._afterAppendToElement = function () {
  if (this._changeable) {
    /** this._updateType(); **/
  }
};
ptsElementMenu.prototype._updateType = function (refreshCheck) {
  if (this._changeable) {
    var type = this._element.get('type');

    this._$.find('[name=type]')
      .removeAttr('checked')
      .filter('[value=' + type + ']')
      .attr('checked', 'checked');
  }
};
ptsElementMenu.prototype.$ = function () {
  return this._$;
};
ptsElementMenu.prototype.init = function () {
  var self = this,
    $original = jQuery('#' + this._menuOriginalId);
  this._$ = $original.clone().attr('id', this._id).appendTo('body');
  this._afterAppendToElement();

  this._fixClickOnRadio();
  this.reposite();
  this._initAddHtmlAttributes();

  if (this._btnsClb) {
    for (var selector in this._btnsClb) {
      if (this._$.find(selector).length) {
        this._$.find(selector)
          .click(function () {
            self._btnsClb[jQuery(this).data('click-clb-selector')]();
            return false;
          })
          .data('click-clb-selector', selector);
      }
    }
  }

  this._initSubMenus();
};
ptsElementMenu.prototype._initAddHtmlAttributes = function () {
  var attrs = new ptsChangeElAttrs(this);
};
ptsElementMenu.prototype._fixClickOnRadio = function () {
  this._$.find('.ptsElMenuBtn').each(function () {
    if (jQuery(this).find('[type=radio]').length) {
      jQuery(this)
        .find('[type=radio]')
        .click(function () {
          jQuery(this).parents('.ptsElMenuBtn:first').click();
        });
    }
  });
};

ptsElementMenu.prototype._hideSubMenus = function () {
  if (!this._$) return;

  var menuAtBottom = this._$.hasClass('ptsElMenuBottom'),
    menuOpenBottom = this._$.hasClass('ptsMenuOpenBottom'),
    self = this;

  this._inAnimation = true;

  this._$.find('.ptsElMenuSubPanel[data-sub-panel]:visible').each(function () {
    jQuery(this).slideUp(self._animationSpeed);
  });

  this._$.removeClass('ptsMenuSubOpened');

  if (!menuAtBottom && !menuOpenBottom && typeof self._originalTop !== 'undefined') {
    this._$.data('animation-in-process', 1).animate(
      {
        top: self._originalTop,
      },
      this._animationSpeed,
      function () {
        if (self._$) {
          self._$.data('animation-in-process', 0);
          self._inAnimation = false;
          delete self._originalTop;
          if (self._isMovable) {
            self._$.trigger('ptsElMenuReposite', [self, null, null, null, false]);
          }
        }
      }
    );
  } else if (menuOpenBottom) {
    this._$.removeClass('ptsMenuOpenBottom');
  }
};

ptsElementMenu.prototype._initSubMenus = function () {
  var self = this;

  if (this._$.find('.ptsElMenuBtn[data-sub-panel-show]').length) {
    this._$.find('.ptsElMenuBtn').click(function () {
      self._hideSubMenus();
    });

    this._$.find('.ptsElMenuBtn[data-sub-panel-show]').click(function () {
      var subPanelShow = jQuery(this).data('sub-panel-show'),
        subPanel = self._$.find('.ptsElMenuSubPanel[data-sub-panel="' + subPanelShow + '"]'),
        menuPos = self._$.position(),
        menuAtBottom = self._$.hasClass('ptsElMenuBottom'),
        menuTop = menuPos.top;

      if (!subPanel.is(':visible')) {
        self._inAnimation = true;
        subPanel.slideDown(self._animationSpeed, function () {
          if (!menuAtBottom) {
            var subPanelHeight = subPanel.outerHeight();

            if (menuTop - subPanelHeight < g_ptsTopBarH) {
              self._$.addClass('ptsMenuOpenBottom');
              self._inAnimation = false;
            } else {
              if (typeof self._originalTop === 'undefined') {
                self._originalTop = parseInt(self._$[0].style.top || self._$.css('top')) || 0;
              }

              self._$.animate(
                {
                  top: menuTop - subPanelHeight,
                },
                self._animationSpeed,
                function () {
                  self._inAnimation = false;
                }
              );
            }
          }
        });

        if (self._element._$.find('a[rel="nofollow"]').length && self._$.find('input[name$="link_rel_nofollow"]').length) {
          self._$.find('input[name$="link_rel_nofollow"]').prop('checked', true);
        }

        self._$.addClass('ptsMenuSubOpened');
      }

      return false;
    });
  }
};

ptsElementMenu.prototype.reposite = function () {
  var elOffset = this._element.$().offset(),
    elWidth = this._element.$().width(),
    //,	elHeight = this._element.$().height()
    width = this._$.width(),
    height = this._$.height(),
    left = elOffset.left - (width - elWidth) / 2,
    top = elOffset.top - height;
  if (this._element.$().hasClass('hover')) {
    top -= g_ptsHoverMargin;
  }
  if (left < 0) left = 0;
  this._$.css({
    left: left + 'px',
    top: top + 'px',
  });
  var elementOffset = this._element.$().offset();
  this._menuOnBottom = elementOffset.top <= g_ptsTopBarH || this._element.$().data('menu-to-bottom');
  if (this._menuOnBottom) {
    this._$.addClass('ptsElMenuBottom');
  }
  if (this._isMovable) {
    this._$.trigger('ptsElMenuReposite', [this, top, left]);
  }
};
ptsElementMenu.prototype.destroy = function () {
  if (this._$) {
    this._$.remove();
    this._$ = null;
  }
};
ptsElementMenu.prototype.getShowEvent = function () {
  return this._showEvent;
};
ptsElementMenu.prototype.show = function () {
  if (!this._$) return; // If menu was already destroyed, with destroy element for example
  if (!this._visible && !_ptsSortInProgress()) {
    // Let's hide all other element menus in current block before show this one
    this.getElement().getBlock().hideElementsMenus(this._showEvent);
    this.reposite();
    // And now - show current menu
    this._$.addClass('active');
    this._visible = true;
  }
};
ptsElementMenu.prototype.inAnimation = function () {
  return this._inAnimation;
};
ptsElementMenu.prototype.hide = function () {
  if (!this._$) return; // If menu was already destroyed, with destroy element for example
  if (this._visible) {
    this._hideSubMenus();
    this._$.removeClass('active');
    this._visible = false;
    if (this._isMovable) {
      this._$.trigger('ptsElMenuHide', this);
    }
    _ptsAddUndoBuffer();
  }
};
ptsElementMenu.prototype.getElement = function () {
  return this._element;
};
ptsElementMenu.prototype._initColorpicker = function (params) {
  params = params || {};
  var self = this,
    color = params.color ? params.color : this._element.get('color'),
    $cpTear = jQuery('#' + self._$.attr('id') + ' .ptsColorPickInputTear'),
    $spanColorPick = jQuery('#' + self._$.attr('id') + ' .ptsInlineColorPicker'),
    oneColorPickerOpt = jQuery.extend(g_ptsVandColorPickerOptions, {
      inline: true,
      altField: $cpTear,
      color: color,
      select: function (event, cpColor) {
        self._element._setColor(cpColor.formatted);
      },
    });
  $spanColorPick.colorpicker(oneColorPickerOpt);
};
ptsElementMenu.prototype.isVisible = function () {
  return this._visible;
};
function ptsElementMenu_btn(menuOriginalId, element, btnsClb) {
  ptsElementMenu_btn.superclass.constructor.apply(this, arguments);
}
extendPts(ptsElementMenu_btn, ptsElementMenu);
ptsElementMenu_btn.prototype._afterAppendToElement = function () {
  ptsElementMenu_btn.superclass._afterAppendToElement.apply(this, arguments);

  this.$().find('.ptsPostLinkDisabled').removeClass('ptsPostLinkDisabled').addClass('ptsPostLinkList');

  // Link settings
  var self = this,
    $btnLink = this._element._getEditArea(),
    $linkInp = this._$.find('[name=btn_item_link]'),
    $titleInp = this._$.find('[name=btn_item_title]'),
    $newWndInp = this._$.find('[name=btn_item_link_new_wnd]'),
    $relNofollow = this._$.find('[name=btn_item_link_rel_nofollow]');

  $linkInp.val($btnLink.attr('href'));

  $linkInp.change(function () {
    $btnLink.attr('href', jQuery(this).val());
    $(document.body).trigger('updateToggleHtml');
  });

  //after change toggle replace href value in sub menu
  jQuery(document.body).on('changeToggleReplaceHrefBtn', function () {
    $linkInp.val($btnLink.attr('href'));
  });

  $titleInp.val($btnLink.attr('title'));
  $titleInp.change(function () {
    $btnLink.attr('title', jQuery(this).val());
  });
  $btnLink.attr('target') == '_blank' ? $newWndInp.attr('checked', 'checked') : $newWndInp.removeAttr('checked');
  $newWndInp.change(function () {
    jQuery(this).prop('checked') ? $btnLink.attr('target', '_blank') : $btnLink.removeAttr('target');
  });
  $relNofollow.change(function () {
    jQuery(this).prop('checked') ? $btnLink.attr('rel', 'nofollow') : $btnLink.removeAttr('rel');
  });
  // Color settings
  this._initColorpicker({
    color: this._element.get('bgcolor'),
  });

  // Tooltip settings
  var $tooltipInp = this._$.find('[name=btn_item_tooltip]'),
    $divElem = this._element._getEditArea().closest('div.ptsCell, div.ptsColFooter, div.ptsColDesc, div.ptsColHeader');

  $tooltipInp.val($divElem.attr('title'));
  $tooltipInp.change(function () {
    $divElem.attr('title', jQuery(this).val());
  });
};
function ptsElementMenu_icon(menuOriginalId, element, btnsClb) {
  ptsElementMenu_icon.superclass.constructor.apply(this, arguments);
}
extendPts(ptsElementMenu_icon, ptsElementMenu);
ptsElementMenu_icon.prototype._afterAppendToElement = function () {
  ptsElementMenu_icon.superclass._afterAppendToElement.apply(this, arguments);

  this.$().find('.ptsPostLinkDisabled').removeClass('ptsPostLinkDisabled').addClass('ptsPostLinkList');

  var self = this,
    iconSizeID = ['fa-lg', 'fa-2x', 'fa-3x', 'fa-4x', 'fa-5x'],
    iconSize = {
      'fa-lg': '1.33333333em',
      'fa-2x': '2em',
      'fa-3x': '3em',
      'fa-4x': '4em',
      'fa-5x': '5em',
    },
    $icon = this._element._$.find('.fa');

  if ($icon.length) {
    var iconClasses = $icon.attr('class').split(' ').reverse(),
      currentIconSize = undefined;

    for (var i in iconClasses) {
      if (iconSizeID.indexOf(iconClasses[i]) != -1) {
        currentIconSize = iconClasses[i];
        break;
      }
    }

    if (currentIconSize) this._$.find('[data-size="' + currentIconSize + '"]').addClass('active');
  }

  this._$.on('click', '[data-size]', function () {
    var classSize = jQuery(this).attr('data-size'),
      $icon = self._element._$.find('.fa');

    if (!$icon.length || !classSize) return;

    $icon.removeClass(iconSizeID.join(' '));
    $icon.addClass(classSize);
    $icon.css('font-size', iconSize[classSize]);
    self._$.find('[data-size].active').removeClass('active');
    self._$.find('[data-size="' + classSize + '"]').addClass('active');
    self._element._block._refreshCellsHeight();
  });

  var btnLink = this._element._getLink(),
    linkInp = this._$.find('[name=icon_item_link]'),
    titleInp = this._$.find('[name=icon_item_title]'),
    newWndInp = this._$.find('[name=icon_item_link_new_wnd]'),
    relNofollow = this._$.find('[name=icon_item_link_rel_nofollow]');

  if (btnLink) {
    linkInp.val(btnLink.attr('href'));
    titleInp.val(btnLink.attr('title'));
    btnLink.attr('target') == '_blank' ? newWndInp.attr('checked', 'checked') : newWndInp.removeAttr('checked');
    btnLink.click(function (e) {
      e.preventDefault();
    });
  }
  relNofollow.change(function () {
    self._element._isRelNofollow(jQuery(this).prop('checked') ? true : false);
  });
  linkInp.change(function () {
    self._element._setLinkAttr('href', jQuery(this).val());
  });
  titleInp.change(function () {
    self._element._setLinkAttr('title', jQuery(this).val());
  });
  newWndInp.change(function () {
    self._element._setLinkAttr('target', jQuery(this).prop('checked') ? true : false);
  });
  // Open links library
  this._$.find('.ptsIconLibBtn').click(function () {
    ptsUtils.showIconsLibWnd(self._element);
    return false;
  });
  // Color settings
  this._initColorpicker();

  // Tooltip settings
  var $tooltipInp = this._$.find('[name=icon_item_tooltip]'),
    $divElem = this._element._getEditArea().closest('div.ptsCell, div.ptsColFooter, div.ptsColDesc, div.ptsColHeader');

  $tooltipInp.val($divElem.attr('title'));
  $tooltipInp.change(function () {
    $divElem.attr('title', jQuery(this).val());
  });
};
function ptsElementMenu_img(menuOriginalId, element, btnsClb) {
  ptsElementMenu_img.superclass.constructor.apply(this, arguments);
}
extendPts(ptsElementMenu_img, ptsElementMenu);
ptsElementMenu_img.prototype._afterAppendToElement = function () {
  ptsElementMenu_img.superclass._afterAppendToElement.apply(this, arguments);

  this.$().find('.ptsPostLinkDisabled').removeClass('ptsPostLinkDisabled').addClass('ptsPostLinkList');

  this.getElement().get('type') === 'video' ? this.$().find('[name=type][value=video]').attr('checked', 'checked') : this.$().find('[name=type][value=img]').attr('checked', 'checked');

  var self = this;
  var btnLink = this._element._getLink(),
    linkInp = this._$.find('[name=image_item_link]'),
    titleInp = this._$.find('[name=image_item_title]'),
    newWndInp = this._$.find('[name=image_item_link_new_wnd]'),
    relNofollow = this._$.find('[name=image_item_link_rel_nofollow]');

  if (btnLink) {
    linkInp.val(btnLink.attr('href'));
    titleInp.val(btnLink.attr('title'));
    btnLink.attr('target') == '_blank' ? newWndInp.attr('checked', 'checked') : newWndInp.removeAttr('checked');
    btnLink.click(function (e) {
      e.preventDefault();
    });
  }

  relNofollow.change(function () {
    self._element._isRelNofollow(jQuery(this).prop('checked') ? true : false);
  });

  linkInp.change(function () {
    self._element._setLinkAttr('href', jQuery(this).val());
  });

  titleInp.change(function () {
    self._element._setLinkAttr('title', jQuery(this).val());
  });

  newWndInp.change(function () {
    self._element._setLinkAttr('target', jQuery(this).prop('checked') ? true : false);
  });

  // Tooltip settings
  var $tooltipInp = this._$.find('[name=image_item_tooltip]'),
    $divElem = this._element._getEditArea().closest('div.ptsCell, div.ptsColFooter, div.ptsColDesc, div.ptsColHeader');

  $tooltipInp.val($divElem.attr('title'));
  $tooltipInp.change(function () {
    $divElem.attr('title', jQuery(this).val());
  });
};
function ptsElementMenu_table_cell(menuOriginalId, element, btnsClb) {
  ptsElementMenu_table_cell.superclass.constructor.apply(this, arguments);
}
extendPts(ptsElementMenu_table_cell, ptsElementMenu);
ptsElementMenu_table_cell.prototype._afterAppendToElement = function () {
  ptsElementMenu_table_cell.superclass._afterAppendToElement.apply(this, arguments);
  var type = this.getElement().get('type');
  if (!type) type = 'txt';
  this._$.find('[name=type][value=' + type + ']').attr('checked', 'checked');
};
/**
 * Table col menu
 */
function ptsElementMenu_table_col(menuOriginalId, element, btnsClb) {
  ptsElementMenu_table_col.superclass.constructor.apply(this, arguments);
}
extendPts(ptsElementMenu_table_col, ptsElementMenu);
ptsElementMenu_table_col.prototype._afterAppendToElement = function () {
  ptsElementMenu_table_col.superclass._afterAppendToElement.apply(this, arguments);
  var self = this;
  // Enb/Dslb fill color
  var $enbFillColorCheck = this._$.find('[name=enb_fill_color]');
  $enbFillColorCheck.change(function () {
    self.getElement().set('enb-color', jQuery(this).prop('checked') ? 1 : 0);
    self.getElement()._setColor(); // Just update it from existing color
    return false;
  });
  //console.log(this.getElement());
  parseInt(this.getElement().get('enb-color')) ? $enbFillColorCheck.attr('checked', 'checked') : $enbFillColorCheck.removeAttr('checked');
  // Color settings
  this._initColorpicker();
  // Enb/Dslb badge
  var $enbBadgeCheck = this._$.find('[name=enb_badge_col]');
  $enbBadgeCheck.change(function () {
    //self.getElement().set('enb-badge', jQuery(this).attr('checked') ? 1 : 0);
    if (jQuery(this).prop('checked')) {
      self.getElement()._setBadge(); // Just update it from existing badge data
      self.getElement()._showSelectBadgeWnd();
    } else {
      self.getElement()._disableBadge();
    }
    return false;
  });
  parseInt(this.getElement().get('enb-badge')) ? $enbBadgeCheck.attr('checked', 'checked') : $enbBadgeCheck.removeAttr('checked');
  // Badge click
  this._btnsClb['.ptsColBadgeBtn'] = function () {
    $enbBadgeCheck.trigger('click');
    //self.getElement()._showSelectBadgeWnd();
  };
  if (PTS_DATA.isPro) {
    if (typeof ptsMenuColSchedule !== 'undefined') {
      this._scheduler = new ptsMenuColSchedule(this.$(), this.getElement());
    }
  } else {
    this._btnsClb['.ptsScheduleColBtn'] = function () {
      // TODO: Show here some PRO promo
    };
  }
};
function ptsElementMenu_table_cell_icon(menuOriginalId, element, btnsClb) {
  ptsElementMenu_table_cell_icon.superclass.constructor.apply(this, arguments);
}
extendPts(ptsElementMenu_table_cell_icon, ptsElementMenu_icon);
// EDITOR ELEMENTS MENU END
// EDITOR BLOCKS START
/**
 * Base block object - for extending
 * @param {object} blockData all block data from database (block database row)
 */
ptsBlockBase.prototype.destroy = function () {
  this._clearElements();
  this._$.slideUp(
    this._animationSpeed,
    jQuery.proxy(function () {
      this._$.remove();
      g_ptsBlockFabric.removeBlockByIter(this.getIter());
      if (g_ptsAllowAddUndo) {
        _ptsSaveCanvas();
      }
    }, this)
  );
};
ptsBlockBase.prototype.build = function (params) {
  params = params || {};
  var innerHtmlContent = '';
  if (this._data.css && this._data.css != '') {
    innerHtmlContent += '<style type="text/css" class="ptsBlockStyle">' + this._data.css + '</style>';
  }
  if (this._data.html && this._data.html != '') {
    innerHtmlContent += '<div class="ptsBlockContent">' + this._data.html + '</div>';
  }
  innerHtmlContent = '<div class="ptsBlock" id="{{block.view_id}}">' + innerHtmlContent + '</div>';
  if (!this._data.session_id) {
    this._data.session_id = mtRand(1, 999999);
  }
  if (!this._data.view_id) {
    this._data.view_id = 'ptsBlock_' + this._data.session_id;
  }
  var template = twig({
    data: innerHtmlContent,
  });
  var generatedHtml = template.render({
    block: this._data,
  });
  this._$ = jQuery(generatedHtml);
  if (params.insertAfter) {
    this._$.insertAfter(params.insertAfter);
  }
  this._initElements();
  this._initHtml();
};
ptsBlockBase.prototype.set = function (key, value) {
  this._data[key] = value;
};
ptsBlockBase.prototype.setData = function (data) {
  this._data = data;
};
ptsBlockBase.prototype.getData = function () {
  return this._data;
};
ptsBlockBase.prototype.appendToCanvas = function () {
  this._$.appendTo('#ptsCanvas');
};
ptsBlockBase.prototype._initHtml = function () {
  this._beforeInitHtml();
};
ptsBlockBase.prototype._beforeInitHtml = function () {};
ptsBlockBase.prototype._rebuildCss = function () {
  var template = twig({
    data: this._data.css,
  });
  var generatedHtml = template.render({
    table: this._data,
  });
  this.getStyle().html(generatedHtml);
};
ptsBlockBase.prototype._rebuildHtml = function () {
  var template = twig({
    data: this._data.html,
  });
  var generatedHtml = template.render({
    table: this._data,
  });
  this.getHtmlBlock().html(generatedHtml);
};
ptsBlockBase.prototype.getStyle = function () {
  return this._$.find('style.ptsBlockStyle');
};
ptsBlockBase.prototype.getHtmlBlock = function () {
  return this._$.find('div.ptsBlockContent');
};
ptsBlockBase.prototype.setTaggedStyle = function (style, tag, elData) {
  this.removeTaggedStyle(tag);
  var $style = this.getStyle(),
    styleHtml = $style.html(),
    tags = this._getTaggedStyleStartEnd(tag);

  var template = twig({
    data: style,
  });
  var generatedStyle = template.render({
      el: elData,
      table: this._data,
    }),
    fullGeneratedStyleTag = tags.start + '\n' + generatedStyle + '\n' + tags.end;
  if (generatedStyle == undefined || !generatedStyle) return;
  $style.html(styleHtml + fullGeneratedStyleTag);
  this.set('css', this.get('css') + this._revertReplaceContent(fullGeneratedStyleTag));
};
ptsBlockBase.prototype.removeTaggedStyle = function (tag, params) {
  params = params || {};
  var tags = this._getTaggedStyleStartEnd(tag, true),
    $style = params.$style ? params.$style : this.getStyle(),
    styleHtml = params.styleHtml ? params.styleHtml : $style.html(),
    replaceRegExp = new RegExp(tags.start + '(.|[\n\r])+' + tags.end, 'gmi');
  $style.html(styleHtml.replace(replaceRegExp, ''));
  this.set('css', this.get('css').replace(replaceRegExp, ''));
};
ptsBlockBase.prototype.getTaggedStyle = function (tag) {
  // TODO: Finish this method
  var tags = typeof tag === 'string' ? this._getTaggedStyleStartEnd(tag) : tag;
};
ptsBlockBase.prototype._getTaggedStyleStartEnd = function (tag, forRegExp) {
  return {
    start: forRegExp ? '\\/\\*start for ' + tag + '\\*\\/' : '/*start for ' + tag + '*/',
    end: forRegExp ? '\\/\\*end for ' + tag + '\\*\\/' : '/*end for ' + tag + '*/',
  };
};
ptsBlockBase.prototype._initMenuItem = function (newMenuItemHtml, item) {
  if (this['_initMenuItem_' + item.type] && typeof this['_initMenuItem_' + item.type] === 'function') {
    var menuItemName = this.getParam('menu_item_name_' + item.type);
    if (menuItemName && menuItemName != '') {
      newMenuItemHtml.find('.ptsBlockMenuElTitle').html(menuItemName);
    }
    this['_initMenuItem_' + item.type](newMenuItemHtml, item);
  }
};
ptsBlockBase.prototype._initMenuItem_align = function (newMenuItemHtml, item) {
  if (this._data.params && this._data.params.align) {
    //newMenuItemHtml.find('input[name="params[align]"]').val( this._data.params.align.val );
    //newMenuItemHtml.find('.ptsBlockMenuElElignBtn').removeClass('active');
    //newMenuItemHtml.find('.ptsBlockMenuElElignBtn[data-align="'+ this._data.params.align.val+ '"]').addClass('active');
    this._setAlign(this._data.params.align.val, true, newMenuItemHtml);
  }
  var self = this;
  newMenuItemHtml.find('.ptsBlockMenuElElignBtn').click(function () {
    self._setAlign(jQuery(this).data('align'));
  });
};
ptsBlockBase.prototype._clickMenuItem_align = function (options) {
  return false;
};
ptsBlockBase.prototype._setAlign = function (align, ignoreAutoSave, menuItemHtml) {
  var possibleAligns = ['left', 'center', 'right'];
  for (var i in possibleAligns) {
    this._$.removeClass('ptsAlign_' + possibleAligns[i]);
  }
  this._$.addClass('ptsAlign_' + align);
  this.setParam('align', align);

  if (!menuItemHtml) {
    var menuOpt = this._$.data('_contentMenuOpt');
    menuItemHtml = menuOpt.items.align.$node;
  }
  menuItemHtml.find('input[name="params[align]"]').val(align);
  menuItemHtml.find('.ptsBlockMenuElElignBtn').removeClass('active');
  menuItemHtml.find('.ptsBlockMenuElElignBtn[data-align="' + align + '"]').addClass('active');

  if (!ignoreAutoSave) {
    _ptsSaveCanvas();
  }
};
ptsBlockBase.prototype._initMenuItem_bg_img = function (newMenuItemHtml, item) {
  if (this._data.params && this._data.params.bg_img_enb && parseInt(this._data.params.bg_img_enb.val)) {
    newMenuItemHtml.find('input[name="params[bg_img_enb]"]').attr('checked', 'checked');
  }
  var self = this;
  newMenuItemHtml.find('input[name="params[bg_img_enb]"]').change(function () {
    self.setParam('bg_img_enb', jQuery(this).attr('checked') ? 1 : 0);
    self._updateBgImg();
  });
};
ptsBlockBase.prototype._clickMenuItem_bg_img = function (options) {
  var self = this;
  ptsCallWpMedia({
    id: this._$.attr('id'),
    clb: function (opts, attach, imgUrl) {
      // we will use full image url from attach.url always here (not image with selected size imgUrl) - as this is bg image
      // but if you see really big issue with this - just try to do it better - but don't broke everything:)
      self.setParam('bg_img', attach.url);
      self._updateBgImg();
    },
  });
};
ptsBlockBase.prototype._updateBgImg = function (ignoreAutoSave) {
  this._rebuildCss();

  if (!ignoreAutoSave) {
    _ptsSaveCanvas();
  }
};
ptsBlockBase.prototype._clickMenuItem = function (key, options) {
  if (this['_clickMenuItem_' + key] && typeof this['_clickMenuItem_' + key] === 'function') {
    return this['_clickMenuItem_' + key](options);
  }
};
ptsBlockBase.prototype.getContent = function () {
  return this._$.find('.ptsBlockContent:first');
};
ptsBlockBase.prototype._revertReplaceContent = function (content) {
  var revertReplace = [{ key: 'view_id' }];
  for (var i = 0; i < revertReplace.length; i++) {
    var key = revertReplace[i].key,
      value = this.get(key),
      replaceFrom = [value],
      replaceTo = revertReplace[i].raw ? '{{table.' + key + '|raw}}' : '{{table.' + key + '}}';
    if (typeof value === 'string' && revertReplace[i].raw) {
      replaceFrom.push(value.replace(/\s+\/>/g, '>'));
    }
    for (var j = 0; j < replaceFrom.length; j++) {
      content = str_replace(content, replaceFrom[j], replaceTo);
    }
  }
  return content;
};
ptsBlockBase.prototype.getHtml = function () {
  var html = this.getContent().html();
  return this._revertReplaceContent(html);
};
ptsBlockBase.prototype.getCss = function () {
  var css = this.getStyle().html();
  return this._revertReplaceContent(css);
  return css;
};
ptsBlockBase.prototype.getIter = function () {
  return this._iter;
};
ptsBlockBase.prototype.beforeSave = function () {
  if (this._elements && this._elements.length) {
    for (var i = 0; i < this._elements.length; i++) {
      this._elements[i].beforeSave();
    }
  }
};
ptsBlockBase.prototype.afterSave = function () {
  if (this._elements && this._elements.length) {
    for (var i = 0; i < this._elements.length; i++) {
      this._elements[i].afterSave();
    }
  }
};
ptsBlockBase.prototype.mapElementsFromHtml = function ($html, clb) {
  var self = this,
    mapCall = function ($el) {
      var element = self.getElementByIterNum(jQuery($el).data('iter-num'));
      if (element && element[clb]) {
        element[clb]();
      }
    };
  $html.find('.ptsEl').each(function () {
    mapCall(this);
  });
  if ($html.hasClass('ptsEl')) {
    mapCall($html);
  }
};
ptsBlockBase.prototype.replaceElement = function (element, toParamCode, type) {
  // Save current element content - in new element internal data
  var oldElContent = element.$().get(0).outerHTML,
    oldElType = element.get('type'),
    savedContent = element.$().data('pre-el-content');
  if (!savedContent) savedContent = {};
  savedContent[oldElType] = oldElContent;
  // Check if there are already saved prev. data for this type of element

  var newHtmlContent = '';

  if (type == 'btn') {
    var existsBtnHTML = null;

    for (var i in this._elements) {
      if (this._elements[i] instanceof ptsElement_btn && this._elements[i]._$.length) {
        existsBtnHTML = this._elements[i]._$.get(0).outerHTML;

        break;
      }
    }

    if (existsBtnHTML) newHtmlContent = existsBtnHTML;
    else newHtmlContent = jQuery('#ptsElementButtonDefaultTemplate').removeAttr('id').get(0).outerHTML;
  } else {
    newHtmlContent = savedContent[type] ? savedContent[type] : this.getParam(toParamCode);
  }

  // Create and append new element HTML after current element
  var $newHtml = jQuery(newHtmlContent);
  $newHtml.insertAfter(element.$());
  // Destroy current element
  var self = this;
  this.destroyElementByIterNum(element.getIterNum(), function () {
    self._disableContentChange = true;
    // Init new element after prev. one was removed
    var newElements = self._initElementsForArea($newHtml);
    for (var i = 0; i < newElements.length; i++) {
      // Save prev. updated content info - in new elements $()
      newElements[i].$().data('pre-el-content', savedContent);
    }
    self._disableContentChange = false;
    self.contentChanged();
  });
};
ptsBlockBase.prototype.contentChanged = function () {
  if (!this._disableContentChange) {
    this._$.trigger('ptsBlockContentChanged', this);
  }
  // setTimeout(function() {
  // 	_ptsAddUndoBuffer(true);
  // }, 200);
};
ptsBlockBase.prototype.hideElementsMenus = function (showEvent) {
  if (this._elements && this._elements.length) {
    for (var i = 0; i < this._elements.length; i++) {
      if (this._elements[i].menuInAnimation()) return; // Menu is in animation - so we don't need to hide it
      if (showEvent && showEvent != this._elements[i].getMenuShowEvent()) continue;
      this._elements[i].hideMenu();
    }
  }
};
/**
 * Price table block base class
 */
ptsBlock_price_table.prototype.addColumn = function () {
  var $colsWrap = this._getColsContainer(),
    $cols = this._getCols(),
    $col = null,
    self = this;
  if ($cols.length) {
    var $lastCol = $cols.last();
    this.mapElementsFromHtml($lastCol, 'beforeSave');
    $col = $cols.last().clone();
    this.mapElementsFromHtml($lastCol, 'afterSave');
  } else {
    $col = jQuery(this.getParam('new_column_html'));
  }
  if (isRtl) {
    $colsWrap.prepend($col);
  } else {
    $colsWrap.append($col);
  }

  this._initElementsForArea($col);
  this._initCellsEdit($col.find('.ptsCell'));
  this._switchHeadRow({ $cols: $col });
  this._switchDescRow({ $cols: $col });
  this._switchFootRow({ $cols: $col });
  this._initCellsMovable($col);
  this._refreshColNumbers();
  $cols = this._getCols();
  $cols.each(function () {
    var element = self.getElementByIterNum(jQuery(this).data('iter-num'));
    if (element) {
      // Update CSS style if required for updated classes
      element._setColor();
    }
  });
  this.checkColWidthPerc();
};
ptsBlock_price_table.prototype._initCellsEdit = function ($cell) {
  var block = this;

  $cell = $cell ? $cell : this._$.find('.ptsCell');
  $cell.each(function () {
    var $currentCell = jQuery(this);

    // Append cell buttons
    var $btnsShell = jQuery(this).find('.ptsCellEditBtnsShell');
    if ($btnsShell.length) {
      $btnsShell.html('');
    } else {
      $btnsShell = jQuery('<div class="ptsCellEditBtnsShell ptsShowSmooth" />').appendTo(this);
    }
    jQuery(this).on('click', function () {
      clearTimeout(jQuery(this).data('btn-shell-hide-timeout'));
      $btnsShell.addClass('active');

      var elementLeft = $btnsShell.offset().left,
        elementWidth = $btnsShell.outerWidth(),
        elementRight = elementLeft + elementWidth,
        screenWidth = screen.width;

      //off-screen element
      if (screenWidth < elementRight) {
        $btnsShell.addClass('ptsRightArrows');
        $btnsShell.css({ left: -(elementWidth + 5) + 'px' });
        $btnsShell.css({ left: -(elementWidth + 5) + 'px' });
        if (!jQuery('#ptsRightArrows').length) {
          jQuery('head').append('<style id="ptsRightArrows">.ptsCellEditBtnsShell.ptsRightArrows:before{border-left: 5px solid rgba(45, 34, 52, 0.96); border-right: none; left:' + elementWidth + 'px}</style>');
        }
      }

      $btnsShell.find('.ptsTextAlignColumn .ptsTextAlignSwitch.active').removeClass('active');

      switch ($currentCell.css('text-align')) {
        case 'left':
          $btnsShell.find('.ptsTextAlignColumn .ptsTextAlignSwitch[data-align="left"]').addClass('active');
          break;
        case 'center':
          $btnsShell.find('.ptsTextAlignColumn .ptsTextAlignSwitch[data-align="center"]').addClass('active');
          break;
        case 'right':
          $btnsShell.find('.ptsTextAlignColumn .ptsTextAlignSwitch[data-align="right"]').addClass('active');
          break;
      }
    });
    jQuery(this).hover(
      function () {
        clearTimeout(jQuery(this).data('btn-shell-hide-timeout'));
      },
      function () {
        if (!$currentCell.hasClass('ui-sortable-helper')) {
          $currentCell.data(
            'btn-shell-hide-timeout',
            setTimeout(function () {
              if (!$btnsShell.is(':hover')) {
                $btnsShell.removeClass('active');
                $btnsShell.find('.ptsTooltipEditWnd').removeClass('active');
              }
            }, 500)
          );
        }
      }
    );
    $btnsShell.hover(
      function () {
        clearTimeout($currentCell.data('btn-shell-hide-timeout'));
      },
      function () {
        if (!$currentCell.hasClass('ui-sortable-helper')) {
          $currentCell.data(
            'btn-shell-hide-timeout',
            setTimeout(function () {
              $btnsShell.removeClass('active');
              $btnsShell.find('.ptsTooltipEditWnd').removeClass('active');
            }, 500)
          );
        }
      }
    );

    // Move cell btn
    jQuery('#ptsMoveCellBtnExl').clone().removeAttr('id').appendTo($btnsShell);

    jQuery('#ptsTextAlignColumn')
      .clone()
      .removeAttr('id')
      .appendTo($btnsShell)
      .on('click', '.ptsTextAlignSwitch', function () {
        var $this = jQuery(this),
          $cell = jQuery(this).parents('.ptsCell:first'),
          align = $this.attr('data-align');

        align = align.charAt(0).toUpperCase() + align.substr(1);

        $this.parent().find('.ptsTextAlignSwitch').removeClass('active');

        $this.addClass('active');

        $cell.removeClassWild('ptsCellAlign*');

        $cell.addClass('ptsCellAlign' + align);
      });

    // Add row after btn
    jQuery('#ptsAddRowAfterBtnExl')
      .clone()
      .removeAttr('id')
      .appendTo($btnsShell)
      .click(function () {
        block.addRow(jQuery(this).parents('.ptsCell:first').index(), true);
        return false;
      });
    // Add row before btn
    jQuery('#ptsAddRowBeforeBtnExl')
      .clone()
      .removeAttr('id')
      .appendTo($btnsShell)
      .click(function () {
        block.addRow(jQuery(this).parents('.ptsCell:first').index(), false);
        return false;
      });

    jQuery('#ptsAddOneCellInColumn')
      .clone()
      .removeAttr('id')
      .appendTo($btnsShell)
      .click(function () {
        var cell = jQuery(this).parents('.ptsCell:first').index(),
          col = jQuery(this).closest('.ptsCol').index();

        block.addOneRow(cell, col, true);

        return false;
      });
    jQuery('#ptsAddTextInCell')
      .clone()
      .removeAttr('id')
      .appendTo($btnsShell)
      .click(function () {
        block.addTextBlock(jQuery(this).parents('.ptsCell:first'));

        return false;
      });

    // Combining rows
    jQuery('#ptsCombiningPrevBtnExl')
      .clone()
      .removeAttr('id')
      .appendTo($btnsShell)
      .click(function () {
        var cell = jQuery(this).closest('.ptsCol').find('.ptsRows .ptsCell').get(jQuery(this).parents('.ptsCell:first').index());

        block.combiningRow(cell);
        return false;
      });
    jQuery('#ptsCombiningNextBtnExl')
      .clone()
      .removeAttr('id')
      .appendTo($btnsShell)
      .click(function () {
        var cell = jQuery(this).closest('.ptsCol').find('.ptsRows .ptsCell').get(jQuery(this).parents('.ptsCell:first').index());

        block.combiningRow(cell, true);
        return false;
      });

    // Tooltips edit buttons manipulations
    var $tooltipBtnShell = jQuery('#ptsTooltipEditBtnShellExl').clone().removeAttr('id').appendTo($btnsShell);
    $tooltipBtnShell.find('.ptsTooltipEditBtn').click(function () {
      var $tooltipWnd = $tooltipBtnShell.find('.ptsTooltipEditWnd');
      if ($tooltipWnd.hasClass('active')) {
        $tooltipWnd.removeClass('active');
      } else {
        $tooltipWnd.find('[name=tooltip]').val(jQuery(this).parents('.ptsCell:first').attr('title'));
        $tooltipWnd.addClass('active');
      }
      return false;
    });
    $tooltipBtnShell.find('[name=tooltip]').change(function () {
      var tooltip = jQuery.trim(jQuery(this).val());
      if (tooltip && tooltip != '') {
        jQuery(this).parents('.ptsCell:first').attr('title', tooltip);
      } else {
        jQuery(this).parents('.ptsCell:first').removeAttr('title');
      }
    });
    // Remove btn
    jQuery('#ptsRemoveRowBtnExl')
      .clone()
      .removeAttr('id')
      .appendTo($btnsShell)
      .click(function () {
        block.removeRow(jQuery(this).parents('.ptsCell:first'));
        return false;
      });
  });
};
ptsBlock_price_table.prototype._destroyCellsEdit = function ($cell) {
  this._$.find('.ptsCellEditBtnsShell').remove();
};
ptsBlock_price_table.prototype.getColsNum = function () {
  return this._getCols().length;
};
ptsBlock_price_table.prototype.addPtsEl = function (parent) {
  var $cellAppend = jQuery(this.getParam('new_cell_html'));
  parent.before($cellAppend);
  this._disableContentChange = false;
  this._initElementsForArea($cellAppend);
  this._initCellsEdit($cellAppend);
  this._disableContentChange = false;
  this.contentChanged();
};
ptsBlock_price_table.prototype.addTextBlock = function ($cellElement) {
  var $cellAppend = jQuery(this.getParam('new_cell_html')).find('.ptsTog');

  this._disableContentChange = true;
  $cellAppend.appendTo($cellElement);
  this._initElementsForArea($cellAppend);
  this._initCellsEdit($cellAppend);
  this._disableContentChange = false;

  this.contentChanged();
};
ptsBlock_price_table.prototype.addOneRow = function (positionCell, positionCol, isAfter) {
  var $cellAppend = jQuery(this.getParam('new_cell_html')),
    columnObject,
    $cellElement;

  for (var i in this._elements) {
    var elementObject = this._elements[i];

    if (elementObject instanceof ptsElement_table_col && elementObject._colNum == positionCol) {
      columnObject = elementObject;

      break;
    }
  }

  if (!columnObject) return;

  $cellElement = columnObject._$.find(this.getColSelectors().cells.sel).eq(positionCell);

  if (!$cellElement) return;

  this._disableContentChange = true;

  if (isAfter) $cellAppend.insertAfter($cellElement);
  else $cellAppend.insertBefore($cellElement);

  this._initElementsForArea($cellAppend);

  this._initCellsEdit($cellAppend);

  this._disableContentChange = false;

  this.contentChanged();
};
ptsBlock_price_table.prototype.addRow = function (positionIndex, after) {
  this._disableContentChange = true;
  var $cols = this._getCols(true),
    self = this;
  $cols.each(function () {
    var $rowsWrap = jQuery(this).find('.ptsRows'),
      $cell = null;
    if (typeof positionIndex === 'undefined') {
      $cell = jQuery(self.getParam('new_cell_html'));
      $rowsWrap.append($cell);
    } else {
      var $positionCell = $rowsWrap.find('.ptsCell:eq(' + positionIndex + ')');
      self.mapElementsFromHtml($positionCell, 'beforeSave');
      $cell = $positionCell.clone();
      self.mapElementsFromHtml($positionCell, 'afterSave');
      after ? $positionCell.after($cell) : $positionCell.before($cell);
    }
    self._initElementsForArea($cell);
    self._initCellsEdit($cell);
  });
  this._disableContentChange = false;
  this.contentChanged();
};
ptsBlock_price_table.prototype.combiningRow = function (cell1, next) {
  var $cell1 = jQuery(cell1);
  var $cell2 = next ? jQuery($cell1.next('.ptsCell')) : jQuery($cell1.prev('.ptsCell'));

  if ($cell2.length == 0) return;

  this._disableContentChange = true;
  var c1, c2;

  if (next) {
    c1 = $cell1;
    c2 = $cell2;
  } else {
    c1 = $cell2;
    c2 = $cell1;
  }

  this.mapElementsFromHtml(c1, 'beforeSave');
  this.mapElementsFromHtml(c2, 'beforeSave');

  c1.find('.ptsCellEditBtnsShell').remove();
  c2.find('.ptsCellEditBtnsShell').remove();
  c1.html(c1.html() + c2.html());
  c2.remove();

  this.mapElementsFromHtml(c1, 'afterSave');
  this.mapElementsFromHtml(c2, 'afterSave');

  this._initElementsForArea(c1);
  this._initCellsEdit(c1);

  this._disableContentChange = false;
  this.contentChanged();
};
ptsBlock_price_table.prototype.removeRow = function ($cell) {
  var block = this,
    cellIndex = $cell && typeof $cell === 'object' ? $cell.index() : false,
    $cols = this._getCols(true);
  if (cellIndex === false) {
    cellIndex = typeof $cell === 'number' ? $cell : $cols.last().find('.ptsCell').length - 1;
  }
  if (block._data && block._data.params && block._data.params.is_horisontal_row_type && block._data.params.is_horisontal_row_type.val && block._data.params.is_horisontal_row_type.val == 1) {
    setTimeout(function () {
      $cell.animateRemovePts(g_ptsAnimationSpeed);
    }, g_ptsAnimationSpeed);
  } else {
    $cols.each(function () {
      var $rowsWrap = jQuery(this).find('.ptsRows'),
        $removeCell = $rowsWrap.find('.ptsCell:eq(' + cellIndex + ')');
      if ($removeCell && $removeCell.length) {
        var $elements = $removeCell.find('.ptsEl');
        $elements.each(function () {
          block.removeElementByIterNum(jQuery(this).data('iter-num'));
        });
        setTimeout(function () {
          $removeCell.animateRemovePts(g_ptsAnimationSpeed);
        }, g_ptsAnimationSpeed); // Wait animation speed time to finally remove cell html element
      }
    });
  }
  setTimeout(
    function () {
      block.contentChanged();
    },
    2 * g_ptsAnimationSpeed + 50
  ); // See prev lines - timeout for g_ptsAnimationSpeed + animation remove for same time g_ptsAnimationSpeed
};
ptsBlock_price_table.prototype.removeCol = function ($col) {
  var $cols = this._getCols();
  if ($cols.length) {
    var $removeCol = null;
    if (typeof $col === 'object') {
      // Colum jquery obj specified
      $removeCol = $col;
    } else if (typeof $col === 'number') {
      // Column item number specified
      $removeCol = $cols.filter(':eq(' + $col + ')');
    } else {
      // Nothing was specified - remove last column in set
      $removeCol = $cols.last();
    }
    var colElement = this.getElementByIterNum($removeCol.data('iter-num'));
    if (colElement) {
      var self = this;
      colElement.destroy(function () {
        self.contentChanged();
      });
    }
  }
};
ptsBlock_price_table.prototype.getRowsNum = function () {
  return this._getCols().first().find('.ptsRows').find('.ptsCell').length;
};
ptsBlock_price_table.prototype._initHtml = function () {
  ptsBlock_price_table.superclass._initHtml.apply(this, arguments);
  var $colsWrap = this._getColsContainer(),
    self = this,
    axis = 'x';

  if (typeof self._data.params.is_horisontal_row_type !== 'undefined' && self._data.params.is_horisontal_row_type.val === '1') {
    axis = 'y';
  }

  $colsWrap.sortable({
    items: '.ptsCol:not(.ptsTableDescCol)',
    axis: axis,
    handle: '.ptsMoveHandler',
    start: function (e, ui) {
      _ptsSetSortInProgress(true);
      var dragElement = self.getElementByIterNum(ui.item.data('iter-num'));
      if (dragElement) {
        dragElement.onSortStart(axis);
      }
    },
    stop: function (e, ui) {
      _ptsSetSortInProgress(false);
      var dragElement = self.getElementByIterNum(ui.item.data('iter-num'));
      if (dragElement) {
        dragElement.onSortStop();
      }

      var desiredOrder = [],
        unsortedCols = [];

      var $cols = self._getCols(),
        num = 1;

      $cols.each(function () {
        var element = self.getElementByIterNum(jQuery(this).data('iter-num'));

        if (element) {
          if (element._colNum != num) {
            desiredOrder.push(num);
            unsortedCols.push(element);
          }
          var classes = jQuery(this).attr('class'),
            newClasses = '';
          newClasses = (classes.replace(/ptsCol\-\d+/g, '') + ' ptsCol-' + num).replace(/\s+/g, ' ');
          jQuery(this).attr('class', newClasses);
        }

        num++;
      });

      var blockCss = self.get('css'),
        resultCss = '';
      for (var i = 0; i < unsortedCols.length; i++) {
        var el = unsortedCols[i],
          newNum = desiredOrder[i],
          num = unsortedCols[i]._colNum,
          mark = self._getTaggedStyleStartEnd('col color ' + unsortedCols[i]._colNum),
          colCss = '';

        el._setColNum(newNum);

        if (blockCss.indexOf(mark.start) > -1 && blockCss.indexOf(mark.end) > -1) {
          colCss = blockCss.substring(blockCss.indexOf(mark.start), blockCss.indexOf(mark.end) + mark.end.length);
        }

        if (!colCss.length) continue;
        var s = mark.start.replace('col color ' + num, 'col color ' + newNum);
        var e = mark.end.replace('col color ' + num, 'col color ' + newNum);

        colCss = colCss.replace(mark.start, s);
        colCss = colCss.replace(mark.end, e);
        colCss = str_replace_all(colCss, '.ptsCol-' + num, '.ptsCol-' + newNum);
        self.removeTaggedStyle('col color ' + num);
        resultCss += colCss;
      }
      if (!resultCss.length) return;
      self.set('css', self.get('css') + resultCss);
      self._rebuildCss();
      self.contentChanged();
    },
  });
  // Set cols numbers for all columns

  this._refreshColNumbers();
  this._initCellsEdit();
  this._initCellsMovable();
};
ptsBlock_price_table.prototype._refreshColNumbers = function () {
  var self = this,
    $cols = this._getCols(),
    num = 1;
  $cols.each(function () {
    var element = self.getElementByIterNum(jQuery(this).data('iter-num'));
    if (element) {
      element._setColNum(num);
      var classes = jQuery(this).attr('class'),
        newClasses = '';
      newClasses = (classes.replace(/ptsCol\-\d+/g, '') + ' ptsCol-' + num).replace(/\s+/g, ' ');
      jQuery(this).attr('class', newClasses);
    }
    num++;
  });
};
ptsBlock_price_table.prototype._setColorFromColorpicker = function (pcColor, ignoreAutoSave) {
  this.setParam('bg_color', pcColor.formatted);
  this._updateFillColor(ignoreAutoSave);
};
ptsBlock_price_table.prototype._updateFillColor = function (ignoreAutoSave) {
  this._rebuildCss();
  if (!ignoreAutoSave) {
    _ptsSaveCanvas();
  }
};
ptsBlock_price_table.prototype._getDescCol = function () {
  return this._$.find('.ptsTableDescCol');
};
ptsBlock_price_table.prototype.switchDescCol = function (state) {
  var $descCol = this._getDescCol();
  this.setParam('enb_desc_col', state ? 1 : 0);
  if (isRtl) {
    $descCol.closest('.ptsColsWrapper').append($descCol);
  }
  state ? $descCol.addClass('ptsShow').removeClass('ptsHide') : $descCol.addClass('ptsHide').removeClass('ptsShow');
  this.checkColWidthPerc();
};
ptsBlock_price_table.prototype._switchHeadRow = function (params) {
  params = params || {};
  if (typeof params.state === 'undefined') {
    params.state = !parseInt(this.getParam('hide_head_row')); // "!" here is because option is actually for hide
  } else {
    this.setParam('hide_head_row', params.state ? 0 : 1);
  }
  if (typeof params.$cols === 'undefined') {
    params.$cols = this._getCols(true);
  }
  params.$cols.each(function () {
    var $cell = jQuery(this).find('.ptsColHeader');
    if ($cell && $cell.length) {
      params.state ? $cell.addClass('ptsShow').removeClass('ptsHide') : $cell.addClass('ptsHide').removeClass('ptsShow');
    }
  });
};
ptsBlock_price_table.prototype._switchDescRow = function (params) {
  params = params || {};
  if (typeof params.state === 'undefined') {
    params.state = !parseInt(this.getParam('hide_desc_row')); // "!" here is because option is actually for hide
  } else {
    this.setParam('hide_desc_row', params.state ? 0 : 1);
  }
  if (typeof params.$cols === 'undefined') {
    params.$cols = this._getCols(true);
  }
  params.$cols.each(function () {
    var $cell = jQuery(this).find('.ptsColDesc');
    if ($cell && $cell.length) {
      params.state ? $cell.addClass('ptsShow').removeClass('ptsHide') : $cell.addClass('ptsHide').removeClass('ptsShow');
    }
  });
};
ptsBlock_price_table.prototype._switchFootRow = function (params) {
  params = params || {};
  if (typeof params.state === 'undefined') {
    params.state = !parseInt(this.getParam('hide_foot_row')); // "!" here is because option is actually for hide
  } else {
    this.setParam('hide_foot_row', params.state ? 0 : 1);
  }
  if (typeof params.$cols === 'undefined') {
    params.$cols = this._getCols(true);
  }
  params.$cols.each(function () {
    var $cell = jQuery(this).find('.ptsColFooter');
    if ($cell && $cell.length) {
      params.state ? $cell.addClass('ptsShow').removeClass('ptsHide') : $cell.addClass('ptsHide').removeClass('ptsShow');
    }
  });
};
ptsBlock_price_table.prototype.beforeSave = function () {
  ptsBlock_price_table.superclass.beforeSave.apply(this, arguments);
  var $hoveredCol = this._getCols().filter('.hover');
  if ($hoveredCol && $hoveredCol.length) {
    this._backHoverFont($hoveredCol);
    this._$lastHoveredCol = $hoveredCol;
  }
  this._destroyCellsEdit();
};
ptsBlock_price_table.prototype.afterSave = function () {
  ptsBlock_price_table.superclass.afterSave.apply(this, arguments);
  if (this._$lastHoveredCol) {
    this._increaseHoverFont(this._$lastHoveredCol);
    this._$lastHoveredCol = null;
  }
  this._initCellsEdit();
};
ptsBlock_price_table.prototype._initCellsMovable = function ($cols) {
  $cols = $cols ? $cols : this._getCols(true);
  var block = this;
  $cols.each(function () {
    jQuery(this)
      .find('.ptsRows')
      .sortable({
        items: '.ptsCell',
        axis: 'y',
        handle: '.ptsMoveCellBtn',
        // No placeholder for now - it is look nice now without it too
        //,	placeholder: 'ptsCellDragHolder'
        stop: function (event, ui) {
          block._refreshCellsHeight();
        },
      });
  });
};
//EDITOR BLOCKS END
//TABLES EDITOR
var g_ptsMainMenu = null,
  g_ptsFileFrame = null, // File frame for wp media uploader
  g_ptsEdit = true,
  g_ptsTopBarH = 32, // Height of the Top Editor Bar
  g_ptsSortInProgress = false,
  g_ptsEditMode = true, // If this script is loaded - this mean that we in edit mode
  g_ptsUndoBuffer = [],
  g_ptsUndoBufferLength = 10,
  g_ptsUndoCurElement = -1,
  g_ptsAllowAddUndo = false,
  g_ptsVandColorPickerOptions = {
    altAlpha: false,
    showOn: 'alt',
    altProperties: 'background-color',
    altColorFormat: 'rgba(rd,gd,bd,af)',
    okOnEnter: true,
    stop: function () {
      _ptsAddUndoBuffer(true);
    },
    //'mode': 's',
    alpha: true,
    color: 'rgba(255, 255, 255, 0.8)',
    colorFormat: 'RGBA',
    title: 'Pick a color',
    part: { map: { size: 128 }, bar: { size: 128 } },
    parts: ['map', 'bar', 'rgb', 'alpha', 'hex', 'preview'],
    layout: { map: [0, 0, 1, 4], bar: [1, 0, 1, 4], preview: [2, 0, 1, 1], rgb: [2, 1, 1, 1], alpha: [2, 2, 1, 1], hex: [2, 3, 1, 1] },
  };
jQuery(document).ready(function () {
  _ptsInitTwig();
  // Prevent all default browser event - such as links redirecting, forms submit, etc.
  jQuery('#ptsCanvas').on('click', 'a', function (event) {
    event.preventDefault();
  });
  jQuery('.ptsMainSaveBtn').click(function () {
    _ptsSaveCanvas();
    return false;
  });
  jQuery('#ptsUndoButton').click(function () {
    _ptsShowProcessing(true);
    setTimeout(function () {
      _ptsUndoCanvas();
      _ptsShowProcessing(false);
    }, 5);
    return false;
  });
  jQuery('#ptsRedoButton').click(function () {
    _ptsShowProcessing(true);
    setTimeout(function () {
      _ptsRedoCanvas();
      _ptsShowProcessing(false);
    }, 5);
    return false;
  });
});
function _ptsShowProcessing(show) {
  jQuery('#ptsUndoProcess').css('display', show ? 'inline-block' : 'none');
}
function _ptsSetUndoDelay(delay) {
  delay = delay ? delay : 200;
  setTimeout(function () {
    g_ptsAllowAddUndo = true;
  }, delay);
}
function _ptsAddUndoBuffer(isChange, refresh, force) {
  return false;
  if (!g_ptsAllowAddUndo || g_ptsSortInProgress) return;
  if (jQuery(!isChange && '.mce-edit-focus').length > 0) return;
  if (jQuery('.mce-floatpanel:visible:not(.mce-tinymce)').length > 0) return;
  if (jQuery('.ptsCellEditBtnsShell.active').length > 0 || jQuery('.ptsMenuSubOpened.active').length > 0) return;
  if (jQuery('.media-modal:visible').length > 0) return;

  var block = g_ptsBlockFabric._blocks[0];
  block.checkColWidthPerc();
  block._refreshCellsHeight();

  var curLength = g_ptsUndoBuffer.length,
    data = ptsGetFabric().getDataForSave()[0],
    canvasHtml = data['html'].trim(),
    canvasCss = jQuery('.ptsBlockStyle').html();

  if (curLength > 0) {
    var last = g_ptsUndoBuffer[g_ptsUndoCurElement < 0 ? curLength - 1 : g_ptsUndoCurElement];
    if (!force && last['html'] == canvasHtml && last['css'] == canvasCss) return;
  }

  var attrClass = jQuery('.ptsBlock').attr('class'),
    attrStyle = jQuery('.ptsBlock').attr('style'),
    settings = jQuery('.ptsSettingsContent').clone(true, true).html(),
    params = _ptsGetTableBlock().get('params'),
    paramsArr = [];

  for (var key in params) {
    paramsArr.push({ key: key, val: params[key].val });
  }
  _ptsInitSettings(false);

  if (refresh) {
    if (g_ptsUndoCurElement >= 0) {
      g_ptsUndoBuffer[g_ptsUndoCurElement] = { html: canvasHtml, css: canvasCss, settings: settings, attrClass: attrClass, attrStyle: attrStyle, params: paramsArr };
    }
  } else {
    if (g_ptsUndoCurElement >= 0) {
      var delta = curLength - g_ptsUndoCurElement - 1;
      if (delta > 0) {
        g_ptsUndoBuffer.splice(g_ptsUndoCurElement + 1, delta);
      }
    }
    if (curLength > g_ptsUndoBufferLength) {
      g_ptsUndoBuffer.shift();
    }
    g_ptsUndoBuffer.push({ html: canvasHtml, css: canvasCss, settings: settings, attrClass: attrClass, attrStyle: attrStyle, params: paramsArr });
    g_ptsUndoCurElement = -1;
    if (g_ptsUndoBuffer.length > 1) {
      jQuery('#ptsUndoButton').removeAttr('disabled');
    }
    jQuery('#ptsRedoButton').attr('disabled', 'disabled');
  }
  g_ptsAllowAddUndo = false;
  _ptsSetUndoDelay();
}
function _ptsRefreshTable(num) {
  var $tbl = jQuery('.ptsBlockContent').closest('.ptsBlock'),
    block = g_ptsBlockFabric._blocks[0],
    settings = jQuery('.ptsSettingsContent');

  block.setRaw($tbl);
  jQuery('.ptsBlock').attr('class', g_ptsUndoBuffer[num]['attrClass']);
  jQuery('.ptsBlock').attr('style', g_ptsUndoBuffer[num]['attrStyle']);
  jQuery('#containerWrapper .ptsSettingsTabs a.nav-tab-active').trigger('click');
  _ptsInitSettings(true);

  settings.find('select.chosen').each(function () {
    var $this = jQuery(this),
      selected = $this.parent().find('a.chosen-single span').text();
    $this.find('option').removeAttr('selected');
    $this.find('option[value="' + selected + '"]').prop('selected', true);
  });

  settings.find('.chosen-container').remove();
  settings.find('.chosen').chosen({ disable_search_threshold: 5 });

  var block = _ptsGetTableBlock(),
    paramsArr = g_ptsUndoBuffer[num]['params'];
  for (var i in paramsArr) {
    block.setParam(paramsArr[i]['key'], paramsArr[i]['val']);
  }
  _ptsAddUndoBuffer(false, true);
}
function _ptsSetTableFromBuffer(num) {
  jQuery('.ptsBlockContent').html(g_ptsUndoBuffer[num]['html']);
  jQuery('.ptsBlockStyle').html(g_ptsUndoBuffer[num]['css']);
  jQuery('.ptsSettingsContent').html(g_ptsUndoBuffer[num]['settings']);
  _ptsRefreshTable(num);
}
function _ptsUndoCanvas() {
  var curLength = g_ptsUndoBuffer.length;

  if (curLength == 0) return;
  g_ptsAllowAddUndo = false;
  g_ptsUndoCurElement = g_ptsUndoCurElement < 0 || g_ptsUndoCurElement >= curLength ? curLength - 2 : g_ptsUndoCurElement - 1;
  if (g_ptsUndoCurElement >= 0) {
    _ptsSetTableFromBuffer(g_ptsUndoCurElement);
  }
  jQuery('#ptsRedoButton').removeAttr('disabled');
  if (g_ptsUndoCurElement <= 0) {
    jQuery('#ptsUndoButton').attr('disabled', 'disabled');
  }
  _ptsSetUndoDelay();
}
function _ptsRedoCanvas() {
  var curLength = g_ptsUndoBuffer.length;

  if (curLength == 0) return;
  g_ptsAllowAddUndo = false;
  g_ptsUndoCurElement++;
  if (g_ptsUndoCurElement < curLength) {
    _ptsSetTableFromBuffer(g_ptsUndoCurElement);
  }
  jQuery('#ptsUndoButton').removeAttr('disabled');
  if (g_ptsUndoCurElement >= curLength - 1) {
    jQuery('#ptsRedoButton').attr('disabled', 'disabled');
  }
  _ptsSetUndoDelay();
}
function _ptsSaveCanvasDelay(delay) {
  delay = delay ? delay : 200;
  setTimeout(_ptsSaveCanvas, delay);
}
function _ptsSaveCanvas(params, byHands) {
  if (!!parseInt(toeOptionPts('disable_autosave')) && 'undefined' == typeof byHands) {
    return; // Autosave disabled in admin area
  }
  if (typeof ptsTables === 'undefined' || !ptsTables || !ptsTables.length || (typeof g_ptsIsTableBuilder !== 'undefined' && g_ptsIsTableBuilder)) {
    return;
  }
  if (typeof ptsTables[0].params.enable_switch_toggle != 'undefined' && ptsTables[0].params.enable_switch_toggle.val == 0) {
    //toggle options not enabled
  } else {
    //toggle enabled
    jQuery(document.body).trigger('updateToggleHtml');
  }

  params = params || {};
  savedData = ptsGetFabric().getDataForSave()[0]; //[0] - is because only one block (table) is in this plugin saved
  var dataForSave = {
    mod: 'tables',
    action: 'save',
    pts_nonce: PTS_NONCE['pts_nonce'],
    data: savedData,
  };
  if (params.sendData) {
    for (var key in params.sendData) {
      dataForSave.data[key] = params.sendData[key];
    }
  }
  jQuery.sendFormPts({
    btn: jQuery('.ptsTableSaveBtn'),
    data: dataForSave,
    onSuccess: function (res) {
      if (!res.error) {
      }
    },
  });
}
function _ptsSortInProgress() {
  return g_ptsSortInProgress;
}
function _ptsSetSortInProgress(state) {
  g_ptsSortInProgress = state;
}
function _ptsInitTwig() {
  Twig.extendFunction('adjBs', function (hex, steps) {
    if (!hex) return hex;
    var isRgb = hex.indexOf('rgb') !== -1;
    if (isRgb) {
      var colorObj = tinycolor(hex);
      hex = colorObj.toHex();
    }
    // Steps should be between -255 and 255. Negative = darker, positive = lighter
    steps = Math.max(-255, Math.min(255, steps));
    // Normalize into a six character long hex string
    hex = str_replace(hex, '#', '');
    if (hex.length == 3) {
      hex = str_repeat(hex.substr(0, 1), 2) + str_repeat(hex.substr(1, 1), 2) + str_repeat(hex.substr(2, 1), 2);
    }
    // Split into three parts: R, G and B
    var color_parts = str_split(hex, 2);
    var res = '#';
    for (var i in color_parts) {
      var color = color_parts[i];
      color = hexdec(color); // Convert to decimal
      color = Math.max(0, Math.min(255, color + steps)); // Adjust color
      res += str_pad(dechex(color), 2, '0', 'STR_PAD_LEFT'); // Make two char hex code
    }
    if (isRgb) {
      return tinycolor(res).setAlpha(colorObj.getAlpha());
    }
    return res;
  });
}
// TABLES EDITOR END
