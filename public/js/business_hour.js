/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!***************************************!*\
  !*** ./resources/js/business_hour.js ***!
  \***************************************/
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
window.addEventListener('DOMContentLoaded', function () {
  // ページの読み込み終わってから表示
  document.querySelector('.local-container').classList.remove('hidden');
  new BusinessHoursController();
});
var BusinessHoursController = /*#__PURE__*/function () {
  function BusinessHoursController() {
    _classCallCheck(this, BusinessHoursController);
    // 定休日に設定されている曜日のスタイル反映
    this.loadHolidays();

    // 「時間を追加」ボタンにイベント設定
    var addBtns = document.querySelectorAll('.time__btn-add');
    this.setEvent(addBtns, 'click', this.addBusinessHour.bind(this), true);

    // ×ボタンにイベント設定
    var deleteBtns = document.querySelectorAll('.time__delete');
    this.setEvent(deleteBtns, 'click', this.deleteBusinessHour, true);

    // 「定休日に設定」ボタンにイベント設定
    var holidayBtns = document.querySelectorAll('.time__btn-holiday');
    this.setEvent(holidayBtns, 'click', this.clickHolidayBtn.bind(this), true);

    // 時間と分を選択するselectタグにイベント設定
    var timeSelects = document.querySelectorAll('.time__item-hour, .time__item-minute');
    this.setEvent(timeSelects, 'change', this.addChangedList, true);

    // 病院のSelectタグにイベント設定
    this.hospitalSelect = document.querySelector('select[name="hospital"]');
    this.hospitalSelect.addEventListener('change', this.changeHospital.bind(this));
  }

  // 画面読み込み時の定休日の設定
  _createClass(BusinessHoursController, [{
    key: "loadHolidays",
    value: function loadHolidays() {
      for (var i = 0; i <= 6; i++) {
        if (document.querySelector("input[name=\"is_open".concat(i, "\"]")).value === '0') {
          this.setHoliday(document.querySelector("#btn_holiday".concat(i)).id.slice(-1));
        }
      }
    }

    /**
     * 診療時間の追加
     * @param addBtn「時間を追加」ボタンElement
     */
  }, {
    key: "addBusinessHour",
    value: function addBusinessHour(addBtn) {
      // 曜日番号(0～6)
      var dayOfWeek = addBtn.id.slice(-1);

      // 診療時間を追加するグループ
      var targetBusinessHours = document.querySelector("#business_hours".concat(dayOfWeek));

      // 追加時のID番号と新しいidやnameで使うもの
      var newIdNumber = Number(targetBusinessHours.lastElementChild.id.split('-').pop()) + 1;
      var newId = "".concat(dayOfWeek, "-").concat(newIdNumber);

      // 診療時間を1組コピー
      var cloneElement = targetBusinessHours.children[0].cloneNode(true);
      cloneElement.id = "business_hour".concat(newId);

      // 削除ボタン
      var cloneDeleteBtn = cloneElement.querySelector('.time__delete');
      cloneDeleteBtn.classList.remove('hidden');
      cloneDeleteBtn.id = "delete".concat(newId);
      this.setEvent(cloneDeleteBtn, 'click', this.deleteBusinessHour);

      // 時間のSelectタグ(startとendの2つ)の名前とIDの変更とデフォルト値設定
      var cloneSelectHours = cloneElement.querySelectorAll('.time__item-hour');
      this.setSelectElement(cloneSelectHours, 'hour', newId);

      // 分のSelectタグ(startとendの2つ)の名前とIDの変更とデフォルト値設定
      var cloneSelectMinutes = cloneElement.querySelectorAll('.time__item-minute');
      this.setSelectElement(cloneSelectMinutes, 'minute', newId);

      //「診療時間のDB上のidを表すinputタグ => 値を空にする
      var cloneBusinessHourId = cloneElement.querySelector('.business_hour_id');
      cloneBusinessHourId.name = "business_hour_id".concat(newId);
      cloneBusinessHourId.value = '';

      //added-list(新規追加リスト)に値を追加
      var addedList = document.querySelector('input[name="added-list"]');
      addedList.value += "".concat(newId, ",");
      targetBusinessHours.appendChild(cloneElement);
    }

    /**　追加分の時間と分のSelectタグの名前とID変更、デフォルト値設定 */
  }, {
    key: "setSelectElement",
    value: function setSelectElement(selectElements, timeType, newId) {
      var _this = this;
      selectElements.forEach(function (selectElement) {
        _this.changeSelectName(selectElement, selectElement.name.split('_')[0], timeType, newId);
        selectElement.insertBefore(_this.createDefalutOption(), selectElement.firstChild);
      });
    }

    /**
     * 追加分の時間と分のセレクトタグの名前変更
     * @params timeType 時間か分のどちらか
     * @params timeOption startかendのどちらか
     *  */
  }, {
    key: "changeSelectName",
    value: function changeSelectName(selectElement, timeOption, timeType, newId) {
      selectElement.name = "".concat(timeOption, "_").concat(timeType).concat(newId);
    }

    /**追加分の時間と分のデフォルト値のOptionタグの作成 */
  }, {
    key: "createDefalutOption",
    value: function createDefalutOption() {
      var defalutOption = document.createElement('option');
      defalutOption.value = 'default';
      defalutOption.innerText = '-';
      defalutOption.selected = true;
      defalutOption.hidden = true;
      return defalutOption;
    }

    /**
     * 診療時間の削除
     * @param deleteBtn ×ボタンElement
     */
  }, {
    key: "deleteBusinessHour",
    value: function deleteBusinessHour(deleteBtn) {
      // 曜日番号(0～6)
      var dayOfWeek = deleteBtn.closest('.time__item-business_hours').id.slice(-1);
      // クエリセレクタ―で使用するid名
      var targetId = "".concat(dayOfWeek, "-").concat(deleteBtn.id.split('-').pop());

      // 削除対象の診療時間
      var deleteTarget = document.querySelector("#business_hour".concat(targetId));
      // 削除対象のDB上のidを表すinputタグ
      var businessHourId = document.querySelector("input[name=\"business_hour_id".concat(targetId, "\"]"));

      // 削除対象が既存のものだったら削除リストに追加、新規追加したものを削除する場合は追加リストから削除
      if (businessHourId.value !== '') {
        var deletedList = document.querySelector('input[name="deleted-list"]');
        deletedList.value += "".concat(businessHourId.value, ",");
      } else {
        var addedList = document.querySelector('input[name="added-list"]');
        addedList.value = addedList.value.replace("".concat(targetId, ","), '');
      }
      deleteTarget.remove();
    }

    /**
     * 定休日ボタンクリック時
     * @param holidayBtn「定休日に設定」ボタン
     */
  }, {
    key: "clickHolidayBtn",
    value: function clickHolidayBtn(holidayBtn) {
      var dayOfWeek = holidayBtn.id.slice(-1);
      this.setHoliday(dayOfWeek);

      // 定休日かどうかを表すinputタグの値を変更
      var isOpen = document.querySelector("input[name=\"is_open".concat(dayOfWeek, "\"]"));
      if (isOpen.value === 'true' || isOpen.value === '1') {
        isOpen.value = 'false';
      } else {
        isOpen.value = 'true';
      }
    }

    /**
     * 定休日設定
     * @params dayOfWeek 曜日番号
     */
  }, {
    key: "setHoliday",
    value: function setHoliday(dayOfWeek) {
      // 診療時間グループ
      var targetBusinessHours = document.querySelector("#business_hours".concat(dayOfWeek));
      targetBusinessHours.classList.toggle('none');

      // 定休日のタグ
      var targetHoliday = document.querySelector("#holiday-".concat(dayOfWeek));
      targetHoliday.classList.toggle('none');

      // 定休日ボタンの表示文字変更
      if (targetHoliday.classList.contains('none')) {
        document.querySelector("#btn_holiday".concat(dayOfWeek)).innerHTML = '定休日に設定';
      } else {
        document.querySelector("#btn_holiday".concat(dayOfWeek)).innerHTML = '定休日を解除';
      }

      // 「時間を追加」ボタン
      var addBtn = document.querySelector("#btn_add".concat(dayOfWeek));
      addBtn.classList.toggle('hidden');
    }

    /**
     * 時間が変更された時の処理
     * @param changedElement 時間が変更されたSelectタグ
     */
  }, {
    key: "addChangedList",
    value: function addChangedList(changedElement) {
      // 時間が変更された曜日番号
      var dayOfWeek = changedElement.closest('.time__item-business_hours').id.slice(-1);
      // 変更された診療時間のDB上のid
      var businessHourId = document.querySelector("input[name=\"business_hour_id".concat(dayOfWeek, "-").concat(changedElement.name.split('-').pop(), "\"]")).value;
      // 変更された診療時間のリスト
      var changedList = document.querySelector('input[name="changed-list"]');

      // リストに値がなければ追加
      if (changedList.value === '' || !changedList.value.split(',').includes(businessHourId)) {
        changedList.value += "".concat(businessHourId, ",");
      }
    }

    /**
     * 病院の変更
     */
  }, {
    key: "changeHospital",
    value: function changeHospital() {
      window.location.href = "/business_hour/".concat(this.hospitalSelect.value);
    }

    // イベントリスナー設定
  }, {
    key: "setEvent",
    value: function setEvent(elements, event, callback) {
      var isArray = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;
      if (isArray) {
        elements.forEach(function (element) {
          return element.addEventListener(event, function () {
            return callback(element);
          });
        });
      } else {
        elements.addEventListener(event, function () {
          return callback(elements);
        });
      }
    }
  }]);
  return BusinessHoursController;
}();
/******/ })()
;