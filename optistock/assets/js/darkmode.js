// Apply dark mode immediately on page load to avoid flash
(function () {
  if (localStorage.getItem('optistock_dark_mode') === '1') {
    document.body.classList.add('dark-mode');
  }
})();

$(function () {
  var $body = $('body');
  var $icon = $('#darkModeIcon');

  function isDark() {
    return $body.hasClass('dark-mode');
  }

  function setIcon(dark) {
    if ($icon.length) $icon.text(dark ? 'light_mode' : 'dark_mode');
  }

  // Set icon on load
  setIcon(isDark());

  $('#darkModeToggle').on('click', function () {
    $body.toggleClass('dark-mode');
    var dark = isDark() ? 1 : 0;
    localStorage.setItem('optistock_dark_mode', dark);
    setIcon(dark === 1);

    // Save to server
    $.post(baseUrl + '/modules/settings/save_darkmode.php', { dark_mode: dark });

    // Refresh charts if present
    if (typeof refreshCharts === 'function') refreshCharts();
  });
});
