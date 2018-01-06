var map;
var mapOptions;
var marker;
var markerArray = [];
var latSend;
var longSend;
var bandMapExist;
var infoWindow;
var html;
var myLatLng;
function loadScript(script)
{
	var runfunction = 'loadedScript';
	var session = new Date();
	var dontCache = session.getTime();
	var script = document.createElement('script');
	if(script!="" && script !=undefined)
	{
		if(typeof script == "function")
			runfunction = script;
		else
			runfunction = 'loadDefault';
	}
	script.type = 'text/javascript';
	script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&callback='+runfunction+'&time='+dontCache;
	document.body.appendChild(script);
}
function loadedScript(){}
function loadDefault()
{
	loadMap('map',1,9,20.979392255760622,-89.62217330932617);
}
function loadMap(id,tipoMapa,zoom,lat,lng)
{
	myLatLng = new google.maps.LatLng(lat,lng);
	switch(tipoMapa)
	{
		case 1:
			mapOptions = 
			{
				zoom: zoom,
				center: myLatLng,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			};
		break;
		case 2:
			mapOptions = 
			{
				zoom: zoom,
				center: myLatLng,
				mapTypeId: google.maps.MapTypeId.SATELLITE
			};
		break;
		case 3:
			mapOptions = 
			{
				zoom: zoom,
				center: myLatLng,
				mapTypeId: google.maps.MapTypeId.HYBRID
			};
		break;
		case 4:
			mapOptions = 
			{
				zoom: zoom,
				center: myLatLng,
				mapTypeId: google.maps.MapTypeId.TERRAIN
			};
		break;
		default:
			mapOptions = 
			{
				zoom: zoom,
				center: myLatLng,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			};
		break;
	}
	map = new google.maps.Map(document.getElementById(id),mapOptions);
}
function checkScriptExist()
{
	try
	{
		if(window.google !=undefined)
		{
			if(typeof window.google === 'object' && typeof window.google.maps === 'object' )
			{
				console.log("Mapa Cargado Correctamente");
				return true;
			}
			else
			{
				console.log("Mapa no cargado Correctamente, posiblemente no cuente con conexion a internet");
				return false;
			}
		}
		else
		{
			console.log("Mapa no cargado Correctamente, posiblemente no cuente con conexion a internet");
			return false;
		}
	}
	catch(err)
	{
		console.log("Mapa no Definido, posiblemente no cuente con conexion a internet");
		return false;
	}
}
function mouseClickEvent()
{
    google.maps.event.addListener(map, 'click', function(event) 
	{
		latSend=event.latLng.lat();
		longSend=event.latLng.lng();
		setMarker(event.latLng);
	});
}
function getCoordsFromAddress(direccion)
{
	if(checkScriptExist())
	{
		var geoCoder = new google.maps.Geocoder(direccion);
		var request = {address:direccion};
		geoCoder.geocode(request, function(result, status)
		{
			if(result!=null)
			{
				if(result.length>0)
				{
					latlng = new google.maps.LatLng(result[0].geometry.location.lat(), result[0].geometry.location.lng()); 
					map.setCenter(latlng);
					map.setZoom(14);
				}
				else
				{
					console.log('No se encontraron coincidencias');
				}
			}
			else
			{
				console.log('Tiene un problema con su conexion a internet');
			}
		});
	}
}
function setMarker(location)  // Crear las marcas y colocarlas en el mapa
{
  if(marker!=null)
	  marker.setMap(null);
  marker = new google.maps.Marker(
  {
      position: location, 
      map: map
  });
}
function setCenter(location,zoom)
{
	map.setCenter(location);
	setZoom(zoom);
}
function cargarMarcasEmergencias(arregloDatos)
{
	if(markerArray.length>0)
	{
		for(i=0;i<markerArray.length;i++)
		{
			markerArray[i].setMap(null);
		}
	}
	if(arregloDatos.length>0)
	{
		for(var i = 0; i < arregloDatos.length ; i++)
		{
			latLng = new google.maps.LatLng(arregloDatos[i].latitud,arregloDatos[i].longitud);
			html = '<div style="width:400px; min-height:300px;"><center><h5>Folio: '+arregloDatos[i].folio+'</h5><h5>Emergenica: <span style="font-weight:normal;">'+arregloDatos[i].tipoReporte+'</span></h5><br /><b>Descripcion del Evento</b></center><p style="font-weight:normal;">'+arregloDatos[i].descripcion+'</p></div>';
			marker = new google.maps.Marker({
				position:latLng,
				map:map,
				icon:'/esquemaWeb/core/img/pins/'+arregloDatos[i].marker
			});
			markerArray[markerArray.length] = marker;
			setMessageMarker(marker,html);
		}
	}
}
function setMessageMarker(marker,text)
{
	var infowindow = new google.maps.InfoWindow({content: text});
	google.maps.event.addListener(marker, 'click', function(){infowindow.open(map,marker);});
}
function setZoom(zoom)
{
	map.setZoom(zoom);
}
function deleteMarker()
{
	if(marker!=null)
		marker.setMap(null);
}
function cargarMarkerGPS(lat,lng,texto)
{
	myLatLng = new google.maps.LatLng(lat,lng);
	marker = new google.maps.Marker({position:myLatLng,map:map/*,icon:'http://chart.apis.google.com/chart?chst=d_bubble_text_small&chld=bb|'+texto+'|00ff00|000000'*/});
	setMessageMarker(marker,'<div style="width:100px; text-align:center">'+texto+'</div>');
	return marker;
}
function eliminarMarkerGPS(unidad){
	unidad.setMap(null);
}