$(document).ready(function () {
    // Ensure the menu is open by default
    var nav = $("#navbarSupportedContent");
    var btn = $(".custom_menu-btn");

    // If you want to retain the menu toggle functionality
    btn.click(function (e) {
        e.preventDefault();
        nav.toggleClass("show"); // Change "lg_nav-toggle" to "show" if you want to toggle visibility
        document.querySelector(".custom_menu-btn").classList.toggle("menu_btn-style");
    });

    function getCurrentYear() {
        var d = new Date();
        var currentYear = d.getFullYear();
        $("#displayDate").html(currentYear);
    }

    getCurrentYear();
    
    

});

