var action = document.getElementById("action"); // DIV QUE CAMBIA DEPENDIENDO DE BOTÓN QUE PRESIONES


const eRegistrar = async() => {

    let nombree = document.getElementById("nombree").value;
    let cantidad = document.getElementById("cantidad").value;
    let logotipo = document.getElementById("logotipo").files[0];



    if (nombree.trim() == "" || cantidad<=0 || isNaN(cantidad) || !logotipo) {
        Swal.fire({ title: "ERROR", text: "TIENES CAMPOS VACÍOS", icon: "error" });
        return;
    }

    let datos = new FormData();
    datos.append("nombree", nombree);
    datos.append("cantidad", cantidad);

   if(logotipo){
    datos.append("logotipo", logotipo);
   }
    datos.append('action', 'registrarE');

    let respuesta = await fetch("php/crudEquipo.php",{method:'POST',body:datos});
    let json = await respuesta.json();


    if (json.success == true) {

        document.getElementById("nombree").value="";
        document.getElementById("cantidad").value="";
        document.getElementById("logotipo").value="";

        Swal.fire({ title: "¡REGISTRO EXITOSO!", text: json.mensaje, icon: "success" });
        cargarEquipo();
        bootstrap.Modal.getInstance(document.getElementById("addEquipo")).hide();

    } else {
        Swal.fire({ title: "ERROR", text: json.mensaje, icon: "error" });
    }
};



function previewImage() {
    const fotoInput = document.getElementById('logotipo');
    const preview = document.getElementById('logotipo-preview');
    
    if (fotoInput.files && fotoInput.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block'; 
        }

        reader.readAsDataURL(fotoInput.files[0]); // Leer el archivo como URL
    }
}



const cargarEquipo = async () => {
    let datos = new FormData();
    datos.append("action", "cargarE");
    let respuesta = await fetch("php/crudEquipo.php", { method: 'POST', body: datos });
    let json = await respuesta.json();

    var divEquipos = `
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addEquipo">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-plus-circle-fill" viewBox="0 0 16 16">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3z"/>
        </svg><br>
        AGREGAR EQUIPO
    </button>
    `;

    divEquipos += '<div class="row">'; 

    let fila = 0; 

    json.map(e => {
        if (fila > 4) {
            divEquipos += '</div>'; 
            divEquipos += '<div class="row">'; 
            fila = 0; 
        }

        divEquipos += `
        <div class="col m-2"> 
            <div class="card border shadow"> 


            <div class="d-flex justify-content-center mt-2">
        <button class="btn btn-info mx-2" onclick="mostrarE(${e.idequipo})" data-bs-toggle="modal" data-bs-target="#editEquipo">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" class="bi bi-pencil-square" viewBox="0 0 16 16">
        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
      </svg>
        </button>

        <button class="btn btn-danger mx-2" onclick="delE(${e.idequipo})">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
        <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
    </svg>
        </button>
</div>

                <div class="card-body text-center"> 
                    <img src="assets/${e.logotipo}" width="70px" height="70px" style="border-radius: 10%;"><br>
                    <p style="margin: 0;">NOMBRE:</p> 
                    <b class="mx-1" style="display: block; margin: 0;">"${e.nombree.toUpperCase()}"</b> 
                    <small style="display: block; margin: 0;">CANTIDAD DE JUGADORES:</small>
                    <b class="mx-1" style="display: block; margin: 0;">${e.cantidad}</b>  
                </div>
            </div>
        </div>
        `;

        fila++; 
    });


    divEquipos += '</div>'; 

    action.innerHTML = divEquipos; 
}



const mostrarE = async (idequipo) => {
    let datos = new FormData();
    datos.append("idequipo", idequipo);
    datos.append('action', 'selectE');

    let respuesta = await fetch("php/crudEquipo.php", { method: 'POST', body: datos });
    let json = await respuesta.json();

    if (json.success) { // Asegúrate de verificar que la respuesta es exitosa
        document.getElementById("enombree").value = json.nombree; // Cambié "nombre" a "nombree"
        document.getElementById("ecantidad").value = json.cantidad;
        document.getElementById("elogotipo-preview").src = "img_equipo/" + json.logotipo;
        document.getElementById("eindex").value = json.idequipo;
    } else {
        Swal.fire({ title: "ERROR", text: json.mensaje, icon: "error" });
    }
}



function epreviewImage() {
    const fotoInput = document.getElementById('elogotipo');
    const preview = document.getElementById('elogotipo-preview');
    
    if (fotoInput.files && fotoInput.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block'; 
        }

        reader.readAsDataURL(fotoInput.files[0]); // Leer el archivo como URL
    }
}


const actualizarE = async () => {
    var idequipo = document.querySelector("#eindex").value;
    var nombree = document.querySelector("#enombree").value;
    var cantidad = document.querySelector("#ecantidad").value;
    var logotipo = document.querySelector("#elogotipo").files[0]; 
  
    if (nombree.trim() == "" || cantidad<=0 || isNaN(cantidad) || !logotipo ) {
        Swal.fire({
            title: "ERROR",
            text: "Tienes campos vacíos",
            icon: "error"
        });
        return;
    }

    let datos = new FormData();
    datos.append("idequipo", idequipo);
    datos.append("nombree", nombree);
    datos.append("cantidad", cantidad);
    datos.append("logotipo", logotipo); 
    datos.append('action', 'updateE');

    try {
        let respuesta = await fetch("php/crudEquipo.php", { method: 'POST', body: datos });
        let json = await respuesta.json();

        if (json.success) {
            Swal.fire({ title: "¡ACTUALIZACIÓN ÉXITOSA!", text: json.mensaje, icon: "success" });
            cargarEquipo(); 
            bootstrap.Modal.getInstance(document.getElementById("editEquipo")).hide();
        } else {
            Swal.fire({ title: "ERROR", text: json.mensaje, icon: "error" });
        }

    } catch (error) {
        console.error('Error al actualizar el equipo:', error);
        Swal.fire({ title: "ERROR", text: "Hubo un problema al procesar la solicitud", icon: "error" });
    }
};





function delE(idequipo) {
    Swal.fire({
        icon:"question",
        title: "¿Estás seguro de eliminar este equipo?",
        showDenyButton: true,
        confirmButtonText: "Si, eliminar",
        denyButtonText: "No estoy seguro"
    }).then(async(result) => {
        if (result.isConfirmed) {

            let datos=new FormData();
            datos.append("idequipo",idequipo);
            datos.append("action","deleteE");
            
            const respuesta=await fetch("php/crudEquipo.php",{method:'POST',body:datos});
            let json=await respuesta.json();
            if(json.success==true){
                Swal.fire("El equipo se eliminó exitosamente", "", "success");
            }else{
                Swal.fire("Error al eliminar", "", "error");
            }
            cargarEquipo();
           
        }
    });
}



//JUGADOR BLOQUE DE CÓDIGO


const jRegistrar = async() => {

    let nombre = document.getElementById("nombrej").value;
    let edad = document.getElementById("edad").value;
    let genero = document.getElementById("genero").value;
    let numero = document.getElementById("numero").value;
    let posicion = document.getElementById("posicion").value;
    let pais = document.getElementById("pais").value;
    let equipo = document.getElementById("equipo").value;
    let foto = document.getElementById("fotoj").files[0];



    if (nombre.trim() == "" || numero<=0 || isNaN(numero) || posicion.trim()=="" || pais.trim()=="" || !foto) {
        Swal.fire({ title: "ERROR", text: "TIENES CAMPOS VACÍOS", icon: "error" });
        return;
    }

    let datos = new FormData();
    datos.append("nombre", nombre);
    datos.append("edad", edad);
    datos.append("genero", genero);
    datos.append("numero", numero);
    datos.append("posicion", posicion);
    datos.append("pais", pais);
    datos.append("equipo", equipo);

   if(foto){
    datos.append("foto", foto);
   }
    datos.append('action', 'registrarJ');

    let respuesta = await fetch("php/crudJugador.php",{method:'POST',body:datos});
    let json = await respuesta.json();


    if (json.success == true) {

        document.getElementById("nombrej").value="";
        document.getElementById("edad").value="";
        document.getElementById("numero").value="";
        document.getElementById("posicion").value="";
        document.getElementById("pais").value="";

        Swal.fire({ title: "¡REGISTRO EXITOSO!", text: json.mensaje, icon: "success" });
        cargarJugador();
        bootstrap.Modal.getInstance(document.getElementById("addJugador")).hide();

    } else {
        Swal.fire({ title: "ERROR", text: json.mensaje, icon: "error" });
    }
};



$(document).ready(function() {
    // Función para cargar equipos en el select
    const selectEquipo = async () => {
        let datos = new FormData();
        datos.append("action", "selectEquipos");

        try {
            let respuesta = await fetch("php/crudJugador.php", { method: 'POST', body: datos });
            let json = await respuesta.json();

            let selectEquipo = document.getElementById('equipo');
            selectEquipo.innerHTML = ""; // Limpia el select antes de llenarlo

            if (json.data && json.data.length > 0) {
                // Si hay equipos, añadir las opciones
                json.data.forEach(equipo => {
                    let option = document.createElement('option');
                    option.value = equipo[0]; // ID del equipo
                    option.textContent = equipo[1]; // Nombre del equipo
                    selectEquipo.appendChild(option);
                });
            } else {
                // Si no hay equipos, mostrar un mensaje
                let option = document.createElement('option');
                option.textContent = "No hay equipos registrados";
                selectEquipo.appendChild(option);
            }
        } catch (error) {
            console.error('Error al cargar equipos:', error);
        }
    };

    // Cargar equipos al abrir el modal
    $('#addJugador').on('show.bs.modal', function () {
        selectEquipo(); // Llama a la función para cargar los equipos
    });
});







function previewJugador() {
    const fotoInput = document.getElementById('fotoj');
    const preview = document.getElementById('fotoj-preview');
    
    if (fotoInput.files && fotoInput.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block'; 
        }

        reader.readAsDataURL(fotoInput.files[0]); // Leer el archivo como URL
    }
}




const cargarJugador = async () => {
    let datos = new FormData();
    datos.append("action", "cargarJ");
    let respuesta = await fetch("php/crudJugador.php", { method: 'POST', body: datos });
    let json = await respuesta.json();

    var divJugador = `
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addJugador">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-plus-circle-fill" viewBox="0 0 16 16">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3z"/>
        </svg><br>
        AGREGAR JUGADPR
    </button>
    `;

    divJugador += '<div class="row">'; 

    let fila = 0; 

    json.map(j => {
        if (fila > 5) {
            divJugador += '</div>'; 
            divJugador += '<div class="row">'; 
            fila = 0; 
        }

        divJugador += `
        <div class="col m-2"> 
            <div class="card border shadow"> 


            <div class="d-flex justify-content-center mt-2">
        <button class="btn btn-info mx-2" onclick="mostrarJ(${j.idjugador})" data-bs-toggle="modal" data-bs-target="#editEquipo">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" class="bi bi-pencil-square" viewBox="0 0 16 16">
        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
      </svg>
        </button>

        <button class="btn btn-danger mx-2" onclick="delJ(${j.idjugador})">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
        <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
    </svg>
        </button>
</div>

                <div class="card-body text-center"> 
                    <img src="assets/${j.foto}" width="70px" height="70px" style="border-radius: 10%;"><br>
                    <p style="margin: 0;">NOMBRE:</p> 
                    <b class="mx-1" style="display: block; margin: 0;">"${j.nombre.toUpperCase()}"</b> 
                    <small style="display: block; margin: 0;">CANTIDAD DE JUGADORES:</small>
                    <b class="mx-1" style="display: block; margin: 0;">${j.cantidad}</b>  
                </div>
            </div>
        </div>
        `;

        fila++; 
    });


    divJugador += '</div>'; 

    action.innerHTML = divJugador; 
}

