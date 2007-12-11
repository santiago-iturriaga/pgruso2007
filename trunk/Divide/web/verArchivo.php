<script language="JavaScript" type="text/javascript">

	var http_request = false;

    function makeRequest(url) {

        http_request = false;

        if (window.XMLHttpRequest) { // Mozilla, Safari,...
            http_request = new XMLHttpRequest();
            if (http_request.overrideMimeType) {
                http_request.overrideMimeType('text/xml');
                // Ver nota sobre esta linea al final
            }
        } else if (window.ActiveXObject) { // IE
            try {
                http_request = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                try {
                    http_request = new ActiveXObject("Microsoft.XMLHTTP");
                } catch (e) {}
            }
        }

        if (!http_request) {
            alert('Falla :( No es posible crear una instancia XMLHTTP');
            return false;
        }
        http_request.onreadystatechange = alertContents;
        http_request.open('GET', url, true);
        alert (url);
        http_request.send(null);

    }


    function alertContents() {

        if (http_request.readyState == 4) {
            if (http_request.status == 200) {
                alert(http_request.responseText);
            } else {
                alert('Hubo problemas con la petición:');
            }
        }

    }

	function delay(){
	    setTimeout('cargar()',5000)
	}
	function cargar(){
		makeRequest('http://localhost/Divide/ajax/getArchivo.php');

	}

</script>

<pre id="coso"></pre>
<a href="#" onclick="cargar();">coso</a>