$(document).ready(function(){$('[data-toggle="tooltip"]').tooltip()}),
function(){"use strict";var e=document.querySelector(".cookiealert"),t=document.querySelector(".acceptcookies");e&&(e.offsetHeight,function(e){for(var t=e+"=",o=decodeURIComponent(document.cookie).split(";"),c=0;c<o.length;c++){for(var n=o[c];" "===n.charAt(0);)n=n.substring(1);if(0===n.indexOf(t))return n.substring(t.length,n.length)}return""}("acceptCookies")||e.classList.add("show"),t.addEventListener("click",function(){!function(e,t,o){var c=new Date;c.setTime(c.getTime()+24*o*60*60*1e3);var n="expires="+c.toUTCString();document.cookie=e+"="+t+";"+n+";path=/"}("acceptCookies",!0,1),e.classList.remove("show"),window.dispatchEvent(new Event("cookieAlertAccept"))}))}();
function trierCatalogue(){var e=document.getElementById("formulaireTri"),t=e.selectedIndex;let a="/catalogue-pieces-detachees/?tri="+e.options[t].value;window.location.href=a}$(document).ready(function(){$("#search-user").keyup(function(){$("#result-search").html("");var e=$(this).val();let t=document.getElementById("affichageRechercheDiv");e.length>3?(t.style.display="block",$.ajax({type:"GET",url:"/recherche-catalogue/",data:"recherche="+encodeURIComponent(e),success:function(e){""!=e?$("#result-search").append(e):document.getElementById("result-search").innerHTML='<div class="row"><div class="col text-center"><p class="h2">Nous n\'avons pas ce jeu en stock.</p><p class="h5">Vous pouvez suivre nos arrivages sur la page Facebook !</p></div></div>'}})):t.style.display="none"})});
$( "#file-input" ).change(function(){$( "#Up" ).click();});
function pageCatalogue(){var e=document.getElementById("pagination"),t=e.selectedIndex;let a="/catalogue-pieces-detachees/?page="+e.options[t].value;window.location.href=a}