# ✅ Instrucciones Actualizadas - Flujo de Desarrollo Laravel 12

## 📝 Cambios Aplicados en `.github/copilot-instructions.md`:

### **🚀 Comando de Desarrollo Actualizado:**

**ANTES:**
```bash
composer run dev  # Runs server + queue + vite concurrently
```

**AHORA:**
```bash
composer run dev  # Runs server + queue + vite concurrently with hot reload
```

### **⚠️ Directrices Críticas Agregadas:**

1. **ALWAYS use `composer run dev`** for development (not `php artisan serve`)
2. **DO NOT stop and restart** the development server unnecessarily
3. **Hot reload is active**: changes in Blade, CSS, JS auto-update the browser
4. **Only restart if modifying** `.env` or `config/` files
5. **The server handles** Laravel + Queue processing + Asset compilation automatically

## 🎯 **Impacto:**

- ✅ **Copilot no sugerirá más** usar `php artisan serve` 
- ✅ **No intentará detener/reiniciar** el servidor innecesariamente
- ✅ **Respetará el hot reload** como método principal
- ✅ **Flujo de trabajo optimizado** para Laravel 12

## 🌟 **Beneficios del Nuevo Flujo:**

- **🔥 Hot Reload**: Cambios automáticos sin F5
- **⚡ Queue Processing**: Trabajos en segundo plano
- **🎨 Asset Compilation**: CSS/JS actualizados automáticamente
- **🚀 Experiencia Moderna**: Laravel 12 development stack completo

---

**Comando para aplicar cambios:**
```bash
# Mensaje de commit sugerido:
📝 docs: actualizar flujo desarrollo Laravel 12 con hot reload

- Priorizar composer run dev sobre php artisan serve
- Documentar hot reload automático
- Establecer directrices para evitar reinicios innecesarios
- Optimizar experiencia de desarrollo con Vite + Queue + Server
```