Este sistema já está totalmente implementado de acordo com sua especificação inicial. Vamos, agora, aperfeiçoá-lo para que possamos usá-lo como exemplo em outras palestras sobre o mesmo assunto. Para isso, analise o diretório /doc para checar o status atual. 

Abaixo encontram-se listadas as melhorias que faremos:

- implementar um crud para que possamos gerenciar diversos eventos, e não apenas o PHPeste 2025
    - data/hora de início
    - data/hora de fim
    - título do evento
    - qualquer outro campo que veja necessário
- executar um seeder com os dados do evento hoje implementado diretamente no código (a data do primeiro sorteio foi 03/10/2025, num evento que começou às 17h e terminho às 20h)
- atualizar as tabelas de inscritos e sorteios com o relacionamento (campos e dados) para este evento criado pelo seeder
- atualizar os arquivos para apresentar os textos relacionados aos eventos dinamicamente:
    - resources/views/draws/index.blade.php
    - resources/views/layouts/app.blade.php
    - resources/views/participants/success.blade.php
    - app/Notifications/ParticipantRegistered.php
- não deixe qualquer menção ao PHPeste 2025 no código, apenas eventos dinâmicos. Os dados atualmente hardcoded no código devem ser movidos para o banco de dados.

O sistema não permitirá selecionar qual evento deverar ser ativo. Deverá, em vez disso, levar em consideração as datas/horas limites de início e fim de cada evento para saber qual está sendo realizado.

Se não houver nenhum evento ocorrendo no momento atual, não será possível se inscrever nem sortear.

O crud deve ser implementado com validações em backend, tal qual o cadastro de participantes está implementado. A autenticação já implementada deve ser utilizada para proteger as rotas do crud. Certamente será necessário uma espécia de "menu" para que as rotas de crud ou o sorteio possam ser acessadas. Sinta-se livre para criar uma view blade nova para isso, ou adaptar alguma já existente.

Criar os testes necessários para garantir o funcionamento correto do sistema com as novas funcionalidades implementadas. Todos devem ser executados com sucesso.

Não implemente nada agora. Apenas crie um documento novo em /docs, seguindo o modelo e nomenclatura dos outros documentos, detalhando como implementar cada uma das melhorias listadas acima. Seja o mais detalhado possível, para que qualquer pessoa ou agente LLM possa seguir o passo a passo e implementar as melhorias corretamente. Monte checklists como implementado no documento 01, para facilitar o acompanhamento do progresso.
