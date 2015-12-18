function newsLetter() {

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            datas = xmlhttp.responseText;
            console.log(datas);

        }
    };
    xmlhttp.open("GET", "/sites/all/modules/custom/analytics01/newsLetter.php", true);
    xmlhttp.send();

}
