<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Máquina</title>
    <style>
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1000; 
            left: 0; top: 0; 
            width: 100%; height: 100%; 
            background-color: rgba(0,0,0,0.5); 
        }
        .modal-content {
            background-color: #fff; 
            margin: 10% auto; 
            padding: 20px; 
            border-radius: 5px; 
            width: 400px; 
        }
        .close {
            float: right; 
            font-size: 20px; 
            font-weight: bold;
            cursor: pointer;
        }
        .error-msg {
            color: red;
            font-size: 0.9em;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <h1>Cadastrar Nova Máquina</h1>
    <a href="{{ route('maquinas.index') }}">Voltar à Lista de Máquinas</a>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('maquinas.store') }}" method="POST" style="margin-top: 15px;">
        @csrf

        <div>
            <label for="codigo_tag">Código TAG *:</label><br>
            <input type="text" id="codigo_tag" name="codigo_tag" value="{{ old('codigo_tag') }}" required>
        </div>
        <br>

        <div>
            <label for="nome_maquina">Nome da Máquina *:</label><br>
            <input type="text" id="nome_maquina" name="nome_maquina" value="{{ old('nome_maquina') }}" required>
        </div>
        <br>

        <div>
            <label for="id_modelo">Modelo *:</label><br>
            <select id="id_modelo" name="id_modelo" required>
                <option value="">Selecione o Modelo</option>
                @foreach($modelos as $modelo)
                    <option value="{{ $modelo->id_modelo }}" {{ old('id_modelo') == $modelo->id_modelo ? 'selected' : '' }}>
                        {{ $modelo->nome_modelo }}
                    </option>
                @endforeach
            </select>
            @if(in_array(auth()->user()->perfil->tipo_perfil ?? '', ['Gestor', 'Administrador']))
                <button type="button" onclick="openModal('modalModelo')">+ Novo Modelo</button>
            @endif
        </div>
        <br>

        <div>
            <label for="id_fabricante">Fabricante *:</label><br>
            <select id="id_fabricante" name="id_fabricante" required>
                <option value="">Selecione o Fabricante</option>
                @foreach($fabricantes as $fabricante)
                    <option value="{{ $fabricante->id_fabricante }}" {{ old('id_fabricante') == $fabricante->id_fabricante ? 'selected' : '' }}>
                        {{ $fabricante->nome_fabricante }}
                    </option>
                @endforeach
            </select>
            @if(in_array(auth()->user()->perfil->tipo_perfil ?? '', ['Gestor', 'Administrador']))
                <button type="button" onclick="openModal('modalFabricante')">+ Novo Fabricante</button>
            @endif
        </div>
        <br>

        <div>
            <label for="data_aquisicao">Data de Aquisição *:</label><br>
            <input type="date" id="data_aquisicao" name="data_aquisicao" value="{{ old('data_aquisicao') }}" required>
        </div>
        <br>

        <div>
            <label for="id_setor">Setor *:</label><br>
            <select id="id_setor" name="id_setor" required>
                <option value="">Selecione o Setor</option>
                @foreach($setores as $setor)
                    <option value="{{ $setor->id_setor }}" {{ old('id_setor') == $setor->id_setor ? 'selected' : '' }}>
                        {{ $setor->nome_setor }}
                    </option>
                @endforeach
            </select>
            @if(in_array(auth()->user()->perfil->tipo_perfil ?? '', ['Gestor', 'Administrador']))
                <button type="button" onclick="openModal('modalSetor')">+ Novo Setor</button>
            @endif
        </div>
        <br>

        <div>
            <label for="id_status_maquina">Status Inicial *:</label><br>
            <select id="id_status_maquina" name="id_status_maquina" required>
                <option value="">Selecione o Status</option>
                @foreach($statuses as $status)
                    <option value="{{ $status->id_status_maquina }}" {{ old('id_status_maquina') == $status->id_status_maquina ? 'selected' : '' }}>
                        {{ $status->estado_da_maquina }}
                    </option>
                @endforeach
            </select>
        </div>
        <br>

        <button type="submit">Salvar Máquina</button>
        <a href="{{ route('maquinas.index') }}">Cancelar</a>
    </form>

    @if(in_array(auth()->user()->perfil->tipo_perfil ?? '', ['Gestor', 'Administrador']))
        <!-- MODAL SETOR -->
        <div id="modalSetor" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('modalSetor')">&times;</span>
                <h3>Cadastrar Novo Setor</h3>
                <div id="errorSetor" class="error-msg"></div>
                <form id="formSetor">
                    <label for="modal_nome_setor">Nome do Setor *:</label><br>
                    <input type="text" id="modal_nome_setor" name="nome_setor" required style="width: 100%;"><br><br>
                    <label for="modal_descricao_setor">Descrição:</label><br>
                    <textarea id="modal_descricao_setor" name="descricao" style="width: 100%;"></textarea><br><br>
                    <button type="button" onclick="submitAjaxForm('{{ route('setores.store') }}', 'formSetor', 'id_setor', 'id_setor', 'nome_setor', 'modalSetor')">Salvar Setor</button>
                </form>
            </div>
        </div>

        <!-- MODAL FABRICANTE -->
        <div id="modalFabricante" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('modalFabricante')">&times;</span>
                <h3>Cadastrar Novo Fabricante</h3>
                <div id="errorFabricante" class="error-msg"></div>
                <form id="formFabricante">
                    <label for="modal_nome_fabricante">Nome do Fabricante *:</label><br>
                    <input type="text" id="modal_nome_fabricante" name="nome_fabricante" required style="width: 100%;"><br><br>
                    <label for="modal_telefone">Telefone:</label><br>
                    <input type="text" id="modal_telefone" name="telefone" style="width: 100%;"><br><br>
                    <label for="modal_email">E-mail:</label><br>
                    <input type="email" id="modal_email" name="email" style="width: 100%;"><br><br>
                    <button type="button" onclick="submitAjaxForm('{{ route('fabricantes.store') }}', 'formFabricante', 'id_fabricante', 'id_fabricante', 'nome_fabricante', 'modalFabricante')">Salvar Fabricante</button>
                </form>
            </div>
        </div>

        <!-- MODAL MODELO -->
        <div id="modalModelo" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('modalModelo')">&times;</span>
                <h3>Cadastrar Novo Modelo</h3>
                <div id="errorModelo" class="error-msg"></div>
                <form id="formModelo">
                    <label for="modal_nome_modelo">Nome do Modelo *:</label><br>
                    <input type="text" id="modal_nome_modelo" name="nome_modelo" required style="width: 100%;"><br><br>
                    <label for="modal_id_fabricante">Fabricante *:</label><br>
                    <select id="modal_id_fabricante" name="id_fabricante" required style="width: 100%;">
                        <option value="">Selecione o Fabricante</option>
                        @foreach($fabricantes as $fabricante)
                            <option value="{{ $fabricante->id_fabricante }}">{{ $fabricante->nome_fabricante }}</option>
                        @endforeach
                    </select><br><br>
                    <label for="modal_especificacoes">Especificações Técnicas:</label><br>
                    <textarea id="modal_especificacoes" name="especificacoes_tecnicas" style="width: 100%;"></textarea><br><br>
                    <button type="button" onclick="submitAjaxForm('{{ route('modelos.store') }}', 'formModelo', 'id_modelo', 'id_modelo', 'nome_modelo', 'modalModelo')">Salvar Modelo</button>
                </form>
            </div>
        </div>

        <script>
            function openModal(id) {
                document.getElementById(id).style.display = 'block';
            }

            function closeModal(id) {
                document.getElementById(id).style.display = 'none';
            }

            function submitAjaxForm(url, formId, selectId, valueKey, textKey, modalId) {
                const form = document.getElementById(formId);
                const formData = new FormData(form);

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => response.json().then(data => ({ status: response.status, body: data })))
                .then(res => {
                    if (res.status === 200 || res.status === 201) {
                        const newItem = res.body.data;
                        const select = document.getElementById(selectId);

                        const option = new Option(newItem[textKey], newItem[valueKey], true, true);
                        select.add(option);

                        if (modalId === 'modalFabricante') {
                            const modalFabSelect = document.getElementById('modal_id_fabricante');
                            modalFabSelect.add(new Option(newItem[textKey], newItem[valueKey]));
                        }

                        form.reset();
                        closeModal(modalId);
                    } else {
                        alert(res.body.message || 'Erro ao cadastrar. Verifique os dados inseridos.');
                    }
                })
                .catch(err => console.error('Erro na requisição:', err));
            }
        </script>
    @endif
</body>
</html>