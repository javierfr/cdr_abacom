import { Component } from '@angular/core';
import { SincronizarService } from '../services/sincronizar.service';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-sincronizar',
  standalone: true,
  imports: [CommonModule],  // Importamos HttpClientModule para standalone
  templateUrl: './sincronizar.component.html',
  styleUrls: ['./sincronizar.component.scss']
})
export default class SincronizarComponent {

  selectedFile: File = null;
  showAlert: boolean = false;
  alertMessage: string = '';
  alertType: string = 'danger';

  constructor(private sincronizarService: SincronizarService) { }

  // Método que se ejecuta cuando se selecciona un archivo
  onFileSelected(event: any) {
    this.selectedFile = event.target.files[0];
    if (this.selectedFile) {
      this.showAlert = false;
      console.log('Archivo seleccionado:', this.selectedFile.name);
    } else {
      this.showAlert = true;
      this.alertMessage = 'Por favor selecciona un archivo';
    }
  }

  // Método para manejar la subida del archivo
  onUpload() {
    if (this.selectedFile) {
      this.sincronizarService.uploadExcel(this.selectedFile).subscribe(
        (response) => {
          this.alertMessage = 'Archivo subido con éxito';
          this.alertType = 'success'; // Cambia el tipo de alerta a success
          this.showAlert = true;
          this.hideAlertAfterTimeout(); // Ocultar después de cierto tiempo
          console.log('Archivo subido con éxito', response);
        },
        (error) => {
          this.alertMessage = error.message || 'Error al subir el archivo';
          this.alertType = 'danger'; // Tipo danger para errores
          this.showAlert = true;
          console.error('Error al subir el archivo:', error);
        }
      );
    } else {
      this.alertMessage = 'Por favor selecciona un archivo';
      this.alertType = 'danger'; // Tipo danger para errores
      this.showAlert = true;
    }
  }

  // Método para manejar la sincronización con Zoho
  onSincronizar() {
    this.sincronizarService.sincronizarZoho().subscribe(
      (response) => {
        this.alertMessage = 'Sincronización con Zoho exitosa';
        this.alertType = 'success';
        this.showAlert = true;
        this.hideAlertAfterTimeout();
        console.log('Sincronización con Zoho exitosa', response);
      },
      (error) => {
        this.alertMessage = error.message || 'Error al sincronizar con Zoho';
        this.alertType = 'danger';
        this.showAlert = true;
        console.error('Error al sincronizar con Zoho:', error);
      }
    );
  }

  // Ocultar la alerta después de 3 segundos
  hideAlertAfterTimeout() {
    setTimeout(() => {
      this.showAlert = false;
    }, 3000);
  }

  // Método para cerrar la alerta manualmente
  closeAlert() {
    this.showAlert = false;
  }
}