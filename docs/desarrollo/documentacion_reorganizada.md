# 📁 Reorganización de Documentación - Copilot Instructions

## ✅ **Nueva Directriz Agregada en `.github/copilot-instructions.md`:**

### **📋 Documentation Structure Guidelines**
```markdown
**CRITICAL:** Follow strict documentation organization rules:

- 📁 ALL documentation files must be placed within the docs/ directory
- 📄 ONLY ONE README.md allowed in the project root directory  
- 🚫 NO documentation files in the root directory except README.md
- 📂 Organize by category within docs/
- 🔄 Move existing docs to appropriate docs/ subdirectories if found elsewhere
- 📝 README.md content should be a brief project overview with links to detailed docs
```

## 🔍 **Archivos Identificados para Reorganización:**

### **❌ Archivos en ubicación INCORRECTA (raíz):**
- `MCP_INTEGRATION_UPDATE.md` → debe ir a `docs/desarrollo/`
- `DESARROLLO_ACTUALIZADO.md` → debe ir a `docs/desarrollo/`

### **✅ Archivos en ubicación CORRECTA:**
- `README.md` (raíz) ✅ 
- `docs/sistema_asistencias/*.md` ✅
- `docs/plan_implementacion/*.md` ✅

## 🎯 **Acción Requerida:**

### **1. Crear directorio para documentación de desarrollo:**
```bash
docs/desarrollo/
```

### **2. Mover archivos de documentación:**
```bash
MCP_INTEGRATION_UPDATE.md → docs/desarrollo/mcp_integration_update.md
DESARROLLO_ACTUALIZADO.md → docs/desarrollo/desarrollo_actualizado.md
```

### **3. Actualizar README.md:**
- Mantener solo overview del proyecto
- Agregar enlaces a documentación detallada en `docs/`

## 📝 **Estructura Final Esperada:**
```
proyecto/
├── README.md (solo overview)
├── docs/
│   ├── sistema_asistencias/
│   │   ├── 01_requisitos_funcionales.md
│   │   ├── 02_casos_uso.md
│   │   └── database_documentation.md
│   ├── plan_implementacion/
│   │   └── PLAN_FASES_IMPLEMENTACION.md
│   ├── desarrollo/
│   │   ├── mcp_integration_update.md
│   │   └── desarrollo_actualizado.md
│   └── recursos_desarrollo/
│       └── templates_estilos/
└── otros archivos del proyecto...
```

## 🧠 **Información Almacenada en Memoria:**
- ✅ Nueva directriz documentada en entidad "Instrucciones Copilot"
- ✅ Observaciones sobre estructura de documentación agregadas
- ✅ Reglas de organización establecidas

## 🎯 **Impacto:**
- **Copilot seguirá automáticamente** estas reglas en futuras interacciones
- **Documentación organizada** por categorías lógicas
- **Proyecto más limpio** con estructura clara
- **Fácil navegación** para desarrolladores nuevos

## 📝 **Mensaje de Commit Sugerido:**
```bash
📁 docs: establecer estructura de documentación organizada

- Agregar directrices de estructura de documentación en copilot-instructions
- Establecer regla: solo README.md en raíz, resto en docs/
- Documentar organización por categorías dentro de docs/
- Preparar reorganización de archivos existentes
- Almacenar nueva directriz en memoria del proyecto
```