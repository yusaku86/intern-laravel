/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!**************************************!*\
  !*** ./resources/js/download_csv.js ***!
  \**************************************/
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
window.addEventListener('DOMContentLoaded', function () {
  new DownloadCsvController();
});
var DownloadCsvController = /*#__PURE__*/function () {
  function DownloadCsvController() {
    _classCallCheck(this, DownloadCsvController);
    this.btnBusinessHourCsv = document.querySelector('#btn-businesshour');
    this.btnBusinessHourCsv.addEventListener('click', this.downloadBusinessHourCsv);
    this.btnVacationCsv = document.querySelector('#btn-vacation');
    this.btnVacationCsv.addEventListener('click', this.downloadVacationCsv);
  }

  // 診療時間csvダウンロード
  _createClass(DownloadCsvController, [{
    key: "downloadBusinessHourCsv",
    value: function downloadBusinessHourCsv() {
      location.href = "/download/business_hour/".concat(document.querySelector('select[name="hospital-businesshour"]').value);
    }

    // 長期休暇csvダウンロード
  }, {
    key: "downloadVacationCsv",
    value: function downloadVacationCsv() {
      location.href = "/download/vacation/".concat(document.querySelector('select[name="hospital-vacation"]').value);
    }
  }]);
  return DownloadCsvController;
}();
/******/ })()
;