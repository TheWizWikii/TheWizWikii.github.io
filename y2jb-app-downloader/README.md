# 📺 Y2JB App Downloader Pro & PKG Joiner

Una herramienta web todo-en-uno diseñada para facilitar la obtención e instalación de la aplicación de **YouTube (PPSA01650)** en consolas PlayStation 5. 

Este proyecto está especialmente pensado para usuarios que van a utilizar el exploit **Y2JB** (desarrollado por **gezine**) para realizar el *jailbreak* a su consola.

---

## 🎯 Finalidad del Proyecto

Para ejecutar el exploit **Y2JB**, es un requisito indispensable contar con la aplicación oficial de YouTube instalada en la PS5.

* **Consolas SIN Jailbreak:** La única forma posible de instalar la app de YouTube en versiones específicas de firmware es mediante el método de *Copia de Seguridad y Restauración* (Backup & Restore).
* **Consolas CON Jailbreak:** Si tu consola ya cuenta con un exploit previo o permisos de depuración (Debug Settings), no necesitas pasar por el tedioso proceso de restauración. Puedes instalar la aplicación directamente en formato **PKG**.

Esta herramienta te permite obtener los archivos **PKG base** oficiales y sus archivos **SC (Short Content / Metadata)** correspondientes según la región de tu cuenta, para luego unirlos (*merge*) en un único archivo listo para instalar.

---

## 🚀 Características

* **Filtrado por Firmware:** Descargas organizadas intuitivamente según la versión del software de tu sistema PS5:
  * **v01.000.003:** Para firmwares de 4.03 a 12.40.
  * **v01.000.030:** Para firmwares 12.60 y superiores.
  * **v01.009.253:** Para firmwares 13.40 y superiores.
* **Compatibilidad Multi-región:** Archivos SC disponibles para EE.UU. (USA), Europa (EUR) y Japón (JAP).
* **PKG Joiner Integrado:** Herramienta unidora cliente (browser-side) nativa. No requiere subir tus archivos a ningún servidor externo; la unión del PKG base y el archivo SC se realiza directamente en tu navegador usando la File System Access API o asignación en memoria local.
* **Diseño Pro & Minimalista:** Interfaz responsiva con soporte PWA.

---

## 📖 Modo de Uso

1. **Selecciona tu Firmware:** Revisa la versión de firmware instalada en tu PS5 y ubica la tarjeta correspondiente en la web.
2. **Descarga los Archivos:**
   * Descarga el **Paquete Base (PKG)**.
   * Descarga el archivo **SC (Metadata)** que coincida con la región de tu consola o cuenta de PSN.
3. **Une los Archivos (PKG Joiner):**
   * Desplázate hasta la sección **3. Herramienta Unidora Integrada (PKG Joiner)**.
   * Haz clic en **Añadir Archivos...** (o arrastra y suelta) y selecciona **ambos archivos** descargados (el `.pkg` base y el `_sc.pkg`).
   * Haz clic en **Unir PKGs**.
   * Guarda el archivo unificado resultante (ej. `YouTube_Merged.pkg`).
4. **Instalación en PS5:**
   * Copia el archivo PKG unificado a la raíz de una unidad USB formateada en exFAT.
   * Conecta el USB a tu PS5 y utiliza el menú **Debug Settings > Package Installer** para instalar la app de YouTube.
   * ¡Listo! Ya puedes proceder con los pasos del exploit **Y2JB**.

---

## 🛠️ Tecnologías Utilizadas

* HTML5 / CSS3 (Variables, Flexbox & Grid CSS)
* JavaScript Vanilla (File Streams, File System Access API)
* Progressive Web App (PWA) / Service Worker

---

## 👤 Créditos y Agradecimientos

* **Desarrollo de la herramienta web:** [TheWizWiki](https://github.com/thewizwikii)
* **Exploit Y2JB:** gezine
* **Servidores y parches:** Playstation Network / Prospero Patches

---
