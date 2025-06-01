# ProyectoTFC

/Tutorias online/
│
├── index.php                          # Página de Inicio ✅
├── auth.php                          # Registro/Login combinado ✅
├── perfil.php                        # Perfil + Dashboard ✅
├── buscar.php                       # Búsqueda de tutores ⬜️
├── tutor.php                        # Detalle tutor + reseñas ⬜️
├── reservar.php                     # Agendar sesión ⬜️
├── chat.php                        # Chat y videollamada ⬜️
├── logout.php                      # Logout ✅
├── procesar_login.php              # Procesa login ✅
├── procesar_registro.php           # Procesa registro ✅
│
├── /includes/                       # Archivos comunes y funciones
│   ├── header.php                   # ✅
│   ├── footer.php                   # ✅ (con términos y política)
│   ├── nav.php                     # ✅
│   ├── db.php                      # ✅
│   └── funciones.php               # ⬜️ (pendiente)
│
├── /usuarios/
│   ├── editar_perfil.php           # ⬜️
│   ├── disponibilidad.php         # ⬜️
│
├── /componentes/
│   ├── /auth/
│   │   ├── form_login.php          # ✅
│   │   ├── form_registro.php       # ✅
│   │
│   ├── /perfil/
│   │   ├── info_personal.php       # ✅
│   │   ├── dashboard.php           # ✅
│   │   └── rendimiento.php         # ✅
│   │
│   ├── /tutor/
│   │   ├── biografia.php           # ⬜️
│   │   ├── materias.php            # ⬜️
│   │   ├── calendario.php          # ⬜️
│   │   └── resenas.php             # ⬜️
│   │
│   ├── /chat/
│   │   ├── chat_box.php            # ⬜️
│   │   └── video_button.php        # ⬜️
│   │
│   ├── /home/
│   │   ├── banner.php              # ✅
│   │   ├── como_funciona.php       # ✅
│   │   ├── testimonios.php         # ✅
│   │   └── contacto.php            # ✅
│
├── /assets/
│   ├── /css/
│   │   └── estilos.css             # ✅
│   └── /img/
│       └── logo.png                # ✅
│
├── /sql/
│   └── estructura.sql              # ✅