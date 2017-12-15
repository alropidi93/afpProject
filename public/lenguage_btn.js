$(document).ready(main);
//var cont = 1;

function main() {
    $('.navbar-leng-pc').click(function(){
      $('.submenu-leng').toggle();
    });
};


$(document).ready(mainResp);
function mainResp() {
    $('.navbar-leng').click(function(){
      $('.submenu-leng').toggle();
    });
};
