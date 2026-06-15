import { message } from "laravel-mix/src/Log";
import $ from 'jquery';
import "jquery-validation";
import "jquery-validation/dist/localization/messages_es"
import Axios from "axios";

export const analistas = {
    data() {
        return { 
          analistas: []
        }
    },
    methods: {
        //cargar los analistas de esta sucursal
       async cargar_analistas(){
          const data = {};
          
          const headers = this.headers;
           
          Axios
              .post("/load_analistas", data, {
                  headers,
              })
              .then((response) => {   
                  if (response.data.success) {
                    this.analistas = response.data.data; 
                  }   
              })
              .catch((error) => { 
                  this.alert_error_modal("Error en el servidor, no se cargo el usuario");
                  console.error(error);
              });
        }, 
    },
    mounted() { 
       this.cargar_analistas(); 
      console.log(this.analistas);
    },
};