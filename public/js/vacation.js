/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!**********************************!*\
  !*** ./resources/js/vacation.js ***!
  \**********************************/
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
window.addEventListener('DOMContentLoaded', function () {
  new VacationsController();
});
var VacationsController = /*#__PURE__*/function () {
  function VacationsController() {
    var _this = this;
    _classCallCheck(this, VacationsController);
    this.form = document.querySelector('form[name="vacation_form"]');
    this.addStartDate = document.querySelector('input[name="add_start-date"]'); // 追加する長期休暇の開始日を入力するinputタグ
    this.addEndDate = document.querySelector('input[name="add_end-date"]'); // 追加する長期休暇の終了日を入力するinputタグ
    this.addReason = document.querySelector('input[name="add_reason"]'); // 追加する長期休暇の理由を入力するinputタグ
    this.deleteTarget = document.querySelector('input[name="delete_target"]'); // 削除する長期休暇のidを値としてもつinputタグ(hidden)

    this.selectHospital = document.querySelector('select[name="hospital"]'); // 病院を選択するSelectタグ
    this.selectHospital.addEventListener('change', this.changeHospital.bind(this));
    var addBtn = document.querySelector('#btn_add');
    addBtn.addEventListener('click', this.addVacation.bind(this));
    var deleteBtns = document.querySelectorAll('.btn-delete');
    deleteBtns.forEach(function (deleteBtn) {
      return deleteBtn.addEventListener('click', function (event) {
        return _this.deleteVacation(deleteBtn, event);
      });
    });
  }

  /**
   * 長期休暇追加実行前の確認 ⇒ 期間と理由が全て入力してあれば実行する
   */
  _createClass(VacationsController, [{
    key: "addVacation",
    value: function addVacation() {
      if (this.addStartDate.value !== '' && this.addEndDate.value !== '' && this.addReason.value !== '') {
        this.form.submit();
      }
    }

    /**
     * 長期休暇の削除
     */
  }, {
    key: "deleteVacation",
    value: function deleteVacation($deleteBtn, event) {
      var targetid = $deleteBtn.id.split('-').pop();
      var startDate = document.querySelector("#start_date-".concat(targetid));
      var endDate = document.querySelector("#end_date-".concat(targetid));
      var reason = document.querySelector("#reason-".concat(targetid));
      if (!window.confirm("\u300C".concat(startDate.innerHTML, "\uFF5E").concat(endDate.innerHTML, " ").concat(reason.innerHTML.replace(/\r?\n/g, '').trim(), "\u300D\u3092\u524A\u9664\u3057\u307E\u3059\u3002\u3088\u308D\u3057\u3044\u3067\u3059\u304B?"))) {
        event.preventDefault();
      }
    }

    /**
     * 病院の変更
     */
  }, {
    key: "changeHospital",
    value: function changeHospital() {
      window.location.href = "/vacation/".concat(this.selectHospital.value);
    }
  }]);
  return VacationsController;
}();
/******/ })()
;