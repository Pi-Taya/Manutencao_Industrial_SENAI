# 📌 Log de Progresso do Projeto

---

### 1. ⚙️ Ambiente & Banco de Dados

* **Configuração Inicial**: Conexão entre Laravel (PHP 8.2) e MySQL (XAMPP) estabelecida na base `manutencao_industrial`.
* **Migrations**: 22 tabelas criadas e organizadas na ordem cronológica correta para evitar erros de dependência e chaves estrangeiras (Foreign Keys).
* **Estrutura**: Banco pronto com suporte a múltiplos técnicos por OS (`ordem_servico_tecnico`), histórico de alteração (`historico_servicos`), peças consumidas (`utilizas`) e laudos técnicos (`parecer_tecnicos`).

---

### 2. 🧩 Camada de Modelos (Eloquent ORM)

* **Models Mapeados**: Criados os modelos principais (`User`, `Maquina`, `OrdemServico`, `ParecerTecnico`, `CronogramaPreventiva`, `Utiliza`, etc.).
* **Chaves Personalizadas**: Configurados `$table`, `$primaryKey` (ex: `id_usuario`, `id_maquina`) e `$fillable` em todos os modelos.
* **Relacionamentos**: Mapeadas as conexões `belongsTo`, `hasMany` e `belongsToMany` entre tabelas de domínio.

---

### 3. 🌱 Dados Iniciais (Seeders)

* **Carga de Domínio**: Banco populado via `artisan db:seed` com dados essenciais para o funcionamento:
* Perfis (`Técnico`, `Gestor`, `Administrador`)
* Turnos (`Manhã`, `Tarde`, `Noite`, `Madrugada`)
* Status de Máquinas (`Ativa`, `Em Manutenção`, `Inativa`)
* Tipos de Manutenção (`Preventiva`, `Corretiva`, `Preditiva`, `Inspeção`)
* Setor inicial para cadastro de usuários.


* **Contas de Teste**: Criados usuários padrão (`gestor@manutencao.test` e `tecnico@manutencao.test`) com senhas criptografadas por hash (BCrypt).

---

### 4. 🧪 Testes & Ferramental

* **Ambiente de Testes Ajustado**: Dependências de testes (`Pest PHP` / `PHPUnit`) corrigidas e alinhadas ao PHP 8.2 local via Composer.
* **Validação**: Execução do `artisan test` concluída com **100% de aprovação** (`PASS`).

---

### 🚀 Status Atual

* **Fase Atual**: Base de Dados, Models, Seeders e Testes concluídos com sucesso.
* **Próxima Etapa**: Autenticação (Login/Logout) e Middleware de controle de acesso por perfil (RBAC - Gestor/Técnico/Admin).