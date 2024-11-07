<script>
    document.addEventListener("DOMContentLoaded", function() {

        if ($("#empresa").val() == 5 && id_sede != 16) {
            document.documentElement.style.setProperty('--bgEmpresa', '#C277D1');
        } else if ($("#empresa").val() == 4) {
            document.documentElement.style.setProperty('--bgEmpresa', '#72a2aa');
        } else if ($("#empresa").val() == 5 && id_sede == 16) {
            document.documentElement.style.setProperty('--bgEmpresa', '#e7cf86');
        }
    });

    document.addEventListener("DOMContentLoaded", function() {

        if ($("#id_empresa").val() == 5 && $("#id_sede").val() != 16) {
            document.documentElement.style.setProperty('--bginmater', '#a381aa');
                document.documentElement.style.setProperty('--bginmater1', '#cfa7d6');
                document.documentElement.style.setProperty('--bginmater2', '#f1cbfb');
                document.documentElement.style.setProperty('--bginmater3', '#ffffff');
                document.documentElement.style.setProperty('--bdinamter', '#000000');
                document.documentElement.style.setProperty('--clinamter', '#000000');
                document.documentElement.style.setProperty('--clinamter1', '#000000');

        } else if ($("#id_empresa").val() == 4) {
            document.documentElement.style.setProperty('--bginmater', '#72a2aa');
            document.documentElement.style.setProperty('--bginmater1', '#d7e5e5');
            document.documentElement.style.setProperty('--bginmater2', '#a9d9d8');
            document.documentElement.style.setProperty('--bginmater3', '#ffffff');
            document.documentElement.style.setProperty('--bdinamter', '#72a2aa');
            document.documentElement.style.setProperty('--clinamter', '#72a2aa');
            document.documentElement.style.setProperty('--clinamter1', '#3b5554');
        } else if($("#id_empresa").val() == 5 && $("#id_sede").val() == 16){
            document.documentElement.style.setProperty('--bginmater', '#c5ab5c');
            document.documentElement.style.setProperty('--bginmater1', '#e7cf86');
            document.documentElement.style.setProperty('--bginmater2', '#ede0b8');
            document.documentElement.style.setProperty('--bginmater3', '#ffffff');
            document.documentElement.style.setProperty('--bdinamter', '#000000');
            document.documentElement.style.setProperty('--clinamter', '#000000');
            document.documentElement.style.setProperty('--clinamter1', '#000000');
        }
    });
</script>