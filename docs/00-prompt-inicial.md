No diretório atual, tenho um esqueleto de aplicação Laravel 12 com sail. Sobre ele, crie uma aplicação web cujo objetivo é receber nome, endereço de e-mail e estado de origem dos participantes do PHPeste 2025, que está ocorrendo em Parnaíba, Piauí.

Ao acessar https://claudioquefez.com.br (pois esta será a demonstração de um sistema implementado com a ajuda do Claude Code), os participantes verão um formulário simples com os seguintes campos:
- Nome (obrigatório)
- Endereço de e-mail (obrigatório, deve ser um e-mail válido)
- Estado de origem (dropdown, obrigatório, deve ser um dos estados brasileiros)

O formulário deve ter um botão "Enviar" que, ao ser clicado, validará os dados no backend. Se houver algum erro de validação, o usuário deve ser redirecionado de volta ao formulário com mensagens de erro apropriadas.

Se os dados forem válidos, o participante será salvo em um banco de dados sqlite na tabela `participants` com as colunas `id`, `name`, `email`, `state`, `codigo` `created_at` e `updated_at`. Após o salvamento, o usuário deve ser redirecionado para uma página de agradecimento que exibe uma mensagem: "Obrigado por se inscrever, [Nome]! Guarde o código abaixo, ele será necessário para receber seu brinde no evento caso você seja sorteado." 

Abaixo da mensagem, deve ser exibido, em destaque, um código único gerado aleatoriamente, composto pela combinação única de 5 letras maiúsculas. Antes de gravar o participante no banco de dados, deve ser verificado se o e-mail já está cadastrado. Se estiver, o usuário deve ser redirecionado de volta ao formulário com uma mensagem de erro informando que o e-mail já foi utilizado. As validações realizadas exclusivamente em backend, ao retornar ao front, devem mostrar as mensagens de erro abaixo do campo, seguindo o padrão apresentado na documentação do Bootstrap.

Apague qualquer migration, template, página ou rota criadas pela instalação do Laravel. 

O template do site deve ser simples, mas deve conter cabeçalho e rodapé, e no rodapé a frase: "O código-fonte e os prompts que geraram este site estão disponíveis em https://github.com/leandrowferreira/claudioquefez".

Antes de gravar o registro do participante, deve ser verificado se o código gerado já existe na base de dados. Se existir, deve ser gerado um novo código até que um código único seja encontrado. Um e-mail de confirmação deve ser enviado para o participante com o texto explicando e principalmente (em destaque) o código gerado.

Os sorteios precisam ser armazenados em uma tabela `draws` com as colunas `id`, `participant_id`, `created_at` e `updated_at`. A coluna `participant_id` deve ser uma foreign key referenciando o participante sorteado. Deve ser possível realizar múltiplos sorteios, mas um participante não pode ser sorteado mais de uma vez. Se todos os participantes já tiverem sido sorteados, a aplicação deve exibir uma mensagem informando que não há mais participantes disponíveis para sorteio.

Deve existir uma rota GET para exibir a lista de pessoas já sorteadas (se houver alguma), um botão grande "Sortear" que, ao ser clicado, sorteia um dos participantes cadastrados e exibe o nome, e-mail e estado. Esta rota não precids ser protegida por autenticação. Abaixo destes dados, devem ser exibidos dois botões: "Exibir código" e "Sortear novamente". Ao clicar em "Exibir código", o código do participante sorteado deve ser exibido em destaque para que possa ser validado na hora de entregar o brinde. Ao clicar em "Sortear novamente", um novo participante deve ser sorteado e exibido na tela.

Crie as migrations, models, controllers, rotas, notificação (e-mail) e todos os demais recursos Laravel necessários para implementar a aplicação conforme descrito acima.

Monte o ambiente de testes (sqlite em memória). Crie testes usando o pest. Os testes devem cobrir todas as funcionalidades da aplicação e devem ser todos executados com sucesso.

Crie um markdown no diretório ./docs seguindo a numeração de arquivos existentes, com os dados do projeto e um checklist com as tarefas a serem realizadas para implementar a aplicação web conforme descrito acima. Esta implementação deve ser feita muito rapidamente, então o checklist deve o suficiente para que esta implementação seja realizada. Não crie nada além do markdown com o checklist.

Stack e Configuração Inicial
- Backend em PHP 8.4 com Laravel 12
- Sail para ambiente de desenvolvimento
- Banco de dados SQLite com tabelas para usuários e sorteios realizados
- O envio de e-mail deve ser simples e usar notificações nativas do Laravel
- Em testes, usarei o Mailpit para testar e aprimorar a funcionalidade de envio de e-mails
- Frontend simples com Blade (sem frameworks JS) e Bootstrap 5 para estilização
- Validação de formulários no backend usando as funcionalidades nativas do Laravel implementadas via Requests
- Readme com explicação e instruções de configuração e execução do projeto
