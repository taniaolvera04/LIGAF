var action = document.getElementById("action"); // DIV QUE CAMBIA DEPENDIENDO DE BOTÓN QUE PRESIONES


const Registrar = async() => {

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

    let respuesta = await fetch("php/registroEquipo.php",{method:'POST',body:datos});
    let json = await respuesta.json();


    if (json.success == true) {

        document.getElementById("nombree").value="";
        document.getElementById("cantidad").value="";
        document.getElementById("logotipo").value="";

        Swal.fire({ title: "¡REGISTRO EXITOSO!", text: json.mensaje, icon: "success" });
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



const cargarEquipo=async()=>{
    let datos=new FormData();
    datos.append("action","cargarE");
    let respuesta=await fetch("php/registroEquipo.php",{method:'POST',body:datos});
    let json= await respuesta.json();
    

    var divEquipos=`
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addEquipo">
    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-plus-circle-fill" viewBox="0 0 16 16">
    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3z"/>
  </svg><br>
   AGREGAR EQUIPO
</button>
`

    json.map(e=>{
   
    divEquipos+=`
    <div class="card w-50 m-auto mt-3">
    
    <div class="card-body">
            <div class="col m-2">
            <img src="assets/${e.logotipo}" width="50px" height="50px" style="border-radius: 100%;">
            <b class="mx-1">${e.nombree}</b>
            <small>CANTIDAD DE JUGADORES ${e.cantidad}</small>
        </div>
    </div>
    </div>
    `
    });
    
    action.innerHTML=divEquipos;
    }
    