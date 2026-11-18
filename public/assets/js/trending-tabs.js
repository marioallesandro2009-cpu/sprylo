document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('[data-trending-tabs]').forEach(function (tablist) {
    var scope = tablist.closest('section');
    if (!scope) { return; }
    var panels = scope.querySelectorAll('[data-trending-panel]');

    tablist.querySelectorAll('.tab').forEach(function (tab) {
      tab.addEventListener('click', function () {
        var target = tab.getAttribute('data-panel');
        panels.forEach(function (panel) {
          panel.style.display = panel.getAttribute('data-trending-panel') === target ? '' : 'none';
        });
      });
    });
  });
});