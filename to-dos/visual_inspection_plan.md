# Plano de Implementação: Inspeção Visual e Métricas Objetivas de Imagem (QLO-FEAT-002)

Este documento estabelece o plano detalhado de implementação passo a passo para a funcionalidade de Inspeção Visual e Avaliação de Qualidade de Imagens para Governança (`QLO-FEAT-002`), com base no [RFC-002](file:///home/rafaelsant/codes/QloApps/to-dos/qlovisualinspection.md).

Cada seção principal corresponde a uma branch de desenvolvimento dedicada com escopo atômico, critérios de aceitação e verificações.

---

## 1. `feat/visual-inspection-python-analyzer`

### Objetivo
Construir o núcleo de processamento matemático e computacional de imagens em Python (funções puras e determinísticas de luminância, detecção de bordas/nitidez e validação de dimensões).

### O que esta etapa fará:
1. **Estrutura de diretórios do microserviço Python:**
   - Criar diretório `inspection-service-python/app/` e `inspection-service-python/tests/fixtures/`.
   - Definir `requirements.txt` com `fastapi>=0.100.0`, `uvicorn>=0.23.0`, `pillow>=10.0.0`, `pytest>=7.4.0` e `httpx>=0.24.0` (para client de testes).
2. **Implementar o módulo [`analyzer.py`](file:///home/rafaelsant/codes/QloApps/inspection-service-python/app/analyzer.py):**
   - Configurar `Image.MAX_IMAGE_PIXELS = 10_000_000` para proteção contra bombas de descompressão.
   - Função `validate_dimensions(img)`: checar resolução mínima (largura $\ge 800$ e altura $\ge 600$ ou orientação retrato $\ge 600 \times 800$).
   - Função `calculate_luminance(img)`: converter imagem para grayscale (`L`) e obter a média global $[0..255]$.
   - Função `classify_luminance(luminance)`: classificar em `UNDEREXPOSED` ($< 40$), `OVEREXPOSED` ($> 220$) ou `OPTIMAL`.
   - Função `calculate_sharpness(img)`: aplicar filtro de gradiente/Laplaciano (`ImageFilter.FIND_EDGES`) e calcular a variância da imagem resultante.
   - Função `classify_sharpness(score)`: classificar em `BLURRY` ($< 50.0$) ou `SHARP` ($\ge 50.0$).
   - Função de agregação `evaluate_image(img)`: consolidar métricas, lista de `warnings` (`LOW_RESOLUTION`, `UNDEREXPOSED`, `OVEREXPOSED`, `BLURRY_IMAGE`) e veredito final (`EVIDENCE_VALID` ou `EVIDENCE_REQUIRES_RETAKE`).
3. **Gerador de fixtures sintéticas [`generate_fixtures.py`](file:///home/rafaelsant/codes/QloApps/inspection-service-python/tests/generate_fixtures.py):**
   - Gerar fixtures determinísticas: `room_sharp.jpg` (1920x1080 com linhas nítidas), `room_dark.jpg` (1920x1080 subexposto) e `room_blurry.jpg` (640x480 com Gaussian Blur).
4. **Bateria de testes unitários [`test_analyzer.py`](file:///home/rafaelsant/codes/QloApps/inspection-service-python/tests/test_analyzer.py):**
   - Validar scores numéricos, thresholds e classificações para cada fixture e cenários de borda.

### Verificação
- Execução do pytest: `pytest inspection-service-python/tests/test_analyzer.py -v` (100% aprovado).

---

## 2. `feat/visual-inspection-python-api`

### Objetivo
Expor a lógica do analisador através de uma API HTTP/REST em FastAPI na porta `8102` com conformidade RFC 7807 para erros, correlation ID e logs estruturados.

### O que esta etapa fará:
1. **Implementar servidor FastAPI [`main.py`](file:///home/rafaelsant/codes/QloApps/inspection-service-python/app/main.py):**
   - Endpoint `GET /healthz` retornando `{"status": "UP"}`.
   - Endpoint `POST /v1/visual-inspections` recebendo `multipart/form-data`:
     - `file`: UploadFile (JPEG/PNG)
     - `room_id`: str
     - `inspection_id`: str
     - Header `X-Correlation-ID`: str (com fallback gerado automaticamente)
2. **Validação de formato e tratamento de erros (RFC 7807):**
   - Validar decodificação do binário com `Image.open().verify()` e tratamento de arquivos corrompidos/inválidos retornando HTTP `400 Bad Request` com schema JSON padronizado.
3. **Logging Estruturado:**
   - Log JSON em stdout contendo `timestamp`, `level`, `correlation_id`, `event="IMAGE_INSPECTED"`, métricas e `duration_ms`.
4. **Testes de Integração da API [`test_api.py`](file:///home/rafaelsant/codes/QloApps/inspection-service-python/tests/test_api.py):**
   - Testar endpoint `GET /healthz`.
   - Testar endpoint `POST /v1/visual-inspections` com fixtures válidas, reprovadas e arquivo de texto corrompido.

### Verificação
- Execução dos testes: `pytest inspection-service-python/tests/ -v`.
- Teste manual via cURL na porta `8102`.

---

## 3. `feat/qlovisualinspection-module-scaffold`

### Objetivo
Criar a base do módulo QloApps `qlovisualinspection`, registrando seus metadados, ciclo de vida de instalação/desinstalação e menu de navegação administrativa.

### O que esta etapa fará:
1. **Estrutura de diretórios do módulo:**
   - Criar `modules/qlovisualinspection/`
   - Criar `modules/qlovisualinspection/controllers/admin/`
   - Criar `modules/qlovisualinspection/views/templates/admin/`
2. **Arquivo de configuração e metadados [`config.xml`](file:///home/rafaelsant/codes/QloApps/modules/qlovisualinspection/config.xml):**
   - Registrar informações do módulo, versão `1.0.0`, compatibilidade e autor.
3. **Classe Principal do Módulo [`qlovisualinspection.php`](file:///home/rafaelsant/codes/QloApps/modules/qlovisualinspection/qlovisualinspection.php):**
   - Extensão da classe `Module`.
   - Métodos `install()` e `uninstall()`.
   - Método auxiliar `installTab()` para registrar a `Tab` `AdminVisualInspection` sob o menu de reservas (`AdminParentOrders` / hotel reservation).
   - Método `uninstallTab()` para limpeza da tab ao desinstalar.
   - Internacionalização de textos com `$this->l(...)`.
4. **Controller Administrativo Inicial [`AdminVisualInspectionController.php`](file:///home/rafaelsant/codes/QloApps/modules/qlovisualinspection/controllers/admin/AdminVisualInspectionController.php):**
   - Herdar de `ModuleAdminController`.
   - Implementar método `initContent()` básico com carregamento inicial do template.
5. **Template Smarty Inicial [`inspection_form.tpl`](file:///home/rafaelsant/codes/QloApps/modules/qlovisualinspection/views/templates/admin/inspection_form.tpl):**
   - Estrutura de container com painel bootstrap do QloApps.

### Verificação
- Instalar e testar módulo no painel administrativo do QloApps e verificar se o menu "Inspeção de Quartos" aparece e renderiza sem erros.

---

## 4. `feat/qlovisualinspection-admin-ui`

### Objetivo
Desenvolver a interface administrativa completa com formulário de checklist de governança, seleção de quartos dinâmicos/reais do hotel e área de feedback visual.

### O que esta etapa fará:
1. **Obtenção de dados do hotel no Controller:**
   - No `AdminVisualInspectionController`, consultar quartos reais cadastrados via `HotelRoomInformation` / tabelas de quartos (`ps_htl_room_information`) ou fallback estruturado caso nenhum quarto esteja cadastrado no ambiente local.
2. **Construção do formulário no template Smarty [`inspection_form.tpl`](file:///home/rafaelsant/codes/QloApps/modules/qlovisualinspection/views/templates/admin/inspection_form.tpl):**
   - Seleção de quarto (`<select name="room_id">`).
   - Checklist de 3 itens essenciais de governança:
     - Cama arrumada e enxoval trocado
     - Banheiro higienizado
     - Amenities repostos
   - Campo de upload de imagem (`<input type="file" accept="image/jpeg,image/png">`).
   - Botão de envio "Avaliar e Salvar".
3. **Estilização e Usabilidade:**
   - Componentes visuais utilizando o padrão Bootstrap/FontAwesome nativo do QloApps (ícones, alerts e labels).

### Verificação
- Renderização visual no back-office com preenchimento interativo e seleção de opções.

---

## 5. `feat/qlovisualinspection-curl-client`

### Objetivo
Implementar no controller PHP o cliente de comunicação HTTP multipart com o microserviço Python na porta `8102`, com geração de UUID/Correlation ID e contingência resiliente de timeout.

### O que esta etapa fará:
1. **Validação de entrada no PHP:**
   - Verificar envio do arquivo (`$_FILES['inspection_photo']`).
   - Validar MIME type real com `mime_content_type()` e limite de tamanho ($< 5\text{ MB}$).
2. **Comunicação cURL com o serviço Python:**
   - Montar payload multipart usando `CURLFile`.
   - Gerar Correlation ID via `Tools::passwdGen(16, 'ALPHANUMERIC')` ou UUID e enviar no header `X-Correlation-ID`.
   - Configurar timeout estrito via `CURLOPT_TIMEOUT_MS = 800`.
3. **Tratamento de contingência e fallback de indisponibilidade:**
   - Se o serviço Python responder HTTP 200: extrair métricas JSON e enviar para exibição no template.
   - Se o serviço estiver offline (cURL error 7 / 28 / status $\ne 200$): salvar checklist e registrar mensagem amigável de contingência ("*Métricas automáticas indisponíveis (Serviço local offline). Checklist registrado manualmente.*").
4. **Persistência / Registro:**
   - Armazenar imagem temporária de visualização e associar resultado ao estado da requisição.

### Verificação
- Teste com o serviço Python online (deve receber as métricas e veredito).
- Teste com o serviço Python desligado (deve capturar o timeout em $\le 800\text{ ms}$ e exibir o alerta de contingência sem travar o painel).

---

## 6. `feat/qlovisualinspection-results-view`

### Objetivo
Renderizar a visualização completa do relatório de inspeção: foto do quarto, badges coloridos de conformidade (verde/vermelho), cards de métricas técnicas e avisos de retake.

### O que esta etapa fará:
1. **Renderização de Resultados no Template Smarty:**
   - Card com a imagem submetida (preview).
   - Badge de Veredito:
     - `EVIDENCE_VALID`: Selo verde `label-success` ("EVIDÊNCIA VÁLIDA").
     - `EVIDENCE_REQUIRES_RETAKE`: Selo vermelho `label-danger` ("REFAZER FOTO").
   - Painel de Métricas Técnicas detalhadas:
     - Resolução ($W \times H\text{ px}$)
     - Luminância (Score numérico e status `OPTIMAL`, `UNDEREXPOSED` ou `OVEREXPOSED`)
     - Nitidez (Score numérico e status `SHARP` ou `BLURRY`)
   - Lista explicativa de avisos caso haja reprovação (`warnings` como "Foto muito escura", "Foto desfocada", etc.) com orientações práticas para a supervisora.
2. **Internacionalização completa:**
   - Tradução de todos os termos técnicos e mensagens de advertência via métodos `{l s='...' mod='qlovisualinspection'}`.

### Verificação
- Submeter fixture `room_sharp.jpg` e verificar renderização verde de aprovação.
- Submeter fixture `room_dark.jpg` e verificar exibição de alerta de subexposição.
- Submeter fixture `room_blurry.jpg` e verificar alerta de desfoque.

---

## 7. `test/visual-inspection-e2e-bdd`

### Objetivo
Criar os cenários de testes automatizados e de integração ponta a ponta (BDD / Gherkin) e validações de critérios de aceitação do DoD.

### O que esta etapa fará:
1. **Bateria de testes E2E/BDD (Python/PHP):**
   - Cenário 1: Imagem nítida e bem iluminada é aprovada (`EVIDENCE_VALID`).
   - Cenário 2: Imagem escura gera alerta `UNDEREXPOSED` e `EVIDENCE_REQUIRES_RETAKE`.
   - Cenário 3: Upload inválido/corrompido retorna HTTP 400.
   - Cenário 4: Queda do serviço local não interrompe o QloApps (contingência e timeout $\le 800\text{ ms}$).
2. **Documentação e Scripts de Inicialização:**
   - Documentar instruções de execução do microserviço Python e comandos rápidos de teste cURL no `README.md` do serviço.

### Verificação
- Execução completa da suíte de testes unitários e de integração com 100% de sucesso.
- Validação do DoD (Definition of Done) completo conforme o RFC-002.
