<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal de Criação
    const createModal = document.getElementById('createModal');
    const openCreateModal = document.getElementById('openCreateModal');
    const closeCreateModal = document.getElementById('closeCreateModal');
    const cancelCreateBtn = document.getElementById('cancelCreateBtn');

    function showCreateModal() {
        createModal.classList.remove('hidden');
    }

    function hideCreateModal() {
        createModal.classList.add('hidden');
        document.getElementById('createUserForm').reset();
    }

    openCreateModal?.addEventListener('click', showCreateModal);
    closeCreateModal?.addEventListener('click', hideCreateModal);
    cancelCreateBtn?.addEventListener('click', hideCreateModal);

    // Modal de Edição
    const editModal = document.getElementById('editModal');
    const closeEditModal = document.getElementById('closeEditModal');
    const cancelEditBtn = document.getElementById('cancelEditBtn');

    function hideEditModal() {
        editModal.classList.add('hidden');
        document.getElementById('editUserForm').reset();
    }

    closeEditModal?.addEventListener('click', hideEditModal);
    cancelEditBtn?.addEventListener('click', hideEditModal);

    // Função para editar usuário
    window.openEditModal = function(userId) {
        fetch(`/admin/users/${userId}/edit`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na requisição');
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }

            document.getElementById('edit_name').value = data.name;
            document.getElementById('edit_email').value = data.email;
            document.getElementById('edit_password').value = ''; // Limpa o campo de senha
            document.getElementById('editUserForm').action = `/admin/users/${userId}`;
            editModal.classList.remove('hidden');
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao carregar dados do usuário: ' + error.message);
        });
    };

    // Adicionar o controle de visualização da senha
    document.getElementById('togglePassword')?.addEventListener('click', function() {
        const passwordInput = document.getElementById('edit_password');
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        // Altera o ícone
        this.innerHTML = type === 'password'
            ? '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>'
            : '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>';
    });

    // Fechar modais ao clicar fora
    window.addEventListener('click', function(event) {
        if (event.target === createModal) hideCreateModal();
        if (event.target === editModal) hideEditModal();
    });

    // Fechar modais com ESC
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            hideCreateModal();
            hideEditModal();
        }
    });

    // Configuração do modal de confirmação
    const confirmationModal = document.getElementById('confirmationModal');
    const confirmAction = document.getElementById('confirmAction');
    const cancelConfirmation = document.getElementById('cancelConfirmation');
    let currentActionCallback = null;

    function showConfirmationModal(message, callback) {
        document.getElementById('confirmationMessage').textContent = message;
        confirmationModal.classList.remove('hidden');
        currentActionCallback = callback;
    }

    function hideConfirmationModal() {
        confirmationModal.classList.add('hidden');
        currentActionCallback = null;
    }

    confirmAction.addEventListener('click', () => {
        if (currentActionCallback) {
            currentActionCallback();
        }
        hideConfirmationModal();
    });

    cancelConfirmation.addEventListener('click', hideConfirmationModal);

    // Atualizar a função de toggle status para usar o modal de confirmação
    window.toggleUserStatus = function(userId) {
        const message = 'Tem certeza que deseja alterar o status deste usuário?';
        showConfirmationModal(message, () => {
            fetch(`/admin/users/${userId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                }
            });
        });
    };

    // Fechar modal de confirmação ao clicar fora
    confirmationModal.addEventListener('click', function(event) {
        if (event.target === confirmationModal) {
            hideConfirmationModal();
        }
    });

    // Fechar modal de confirmação com ESC
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            hideConfirmationModal();
        }
    });
});
</script>
