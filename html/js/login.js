// login.js (substitua o arquivo atual por este)
import { Validate } from "./Validate.js";
import { Requests } from "./Requests.js";

// IDs dos botões e forms (de acordo com o HTML corrigido)
const loginBtn = document.getElementById('Login');
const preCadastroBtn = document.getElementById('preCadastro');

// Inicializa máscaras (se o input existir)
try {
    if (window.$ && $.fn.inputmask) {
        $('#cpf').inputmask({ "mask": ["999.999.999-99"] });
        $('#celular').inputmask({ "mask": ["(99) 99999-9999"] });
        $('#whatsapp').inputmask({ "mask": ["(99) 99999-9999"] });
    } else {
        console.warn('Inputmask não encontrado (jQuery/inputmask). Se usa máscaras, verifique inclusão do plugin.');
    }
} catch (err) {
    console.error('Erro ao inicializar inputmask', err);
}

// --- utilitários ---
function limparBackdrop() {
    try {
        document.querySelectorAll(".modal-backdrop").forEach(e => e.remove());
        document.body.classList.remove("modal-open");
        document.body.style.overflow = "";
    } catch (err) {
        console.error('Erro ao limpar backdrop:', err);
    }
}

function fecharModalSeguro(modalId) {
    try {
        const el = document.getElementById(modalId);
        if (!el) return;
        let inst = bootstrap.Modal.getInstance(el);
        if (!inst) inst = new bootstrap.Modal(el);
        inst.hide();
        // timeout pequeno para evitar condição de corrida ao remover backdrop
        setTimeout(() => limparBackdrop(), 50);
    } catch (err) {
        console.error('Erro ao fechar modal seguro:', err);
    }
}

// --- handler do Pre-Cadastro ---
if (preCadastroBtn) {
    preCadastroBtn.addEventListener('click', async (e) => {
        e.preventDefault();
        console.log('[preCadastro] botão clicado');
        try {
            // Ajuste: usar o form separado 'form-precadastro'
            const response = await Requests.SetForm('form-precadastro').Post('/login/precadastro');
            console.log('[preCadastro] resposta:', response);

            if (!response || !response.status) {
                Swal.fire({
                    title: "Atenção!",
                    text: response ? response.msg : 'Erro inesperado',
                    icon: "error",
                    timer: 3000
                });
                return;
            }

            await Swal.fire({
                title: "Sucesso!",
                text: response.msg,
                icon: "success",
                timer: 2000,
                showConfirmButton: false
            });

            // Fecha o modal de precadastro e limpa overlays caso necessário
            fecharModalSeguro('pre-cadastro');

        } catch (error) {
            console.error('[preCadastro] erro:', error);
            Swal.fire({
                title: "Erro",
                text: "Ocorreu um erro ao enviar o pré-cadastro. Veja console.",
                icon: "error"
            });
        }
    });
} else {
    console.warn('Botão preCadastro não encontrado (id="preCadastro")');
}

// --- handler do Login ---
if (loginBtn) {
    loginBtn.addEventListener('click', async (e) => {
        e.preventDefault();
        console.log('[Login] botão clicado');

        try {
            // Ajuste: usar o form separado 'form-login'
            const response = await Requests.SetForm('form-login').Post('/login/autenticar');
            console.log('[Login] resposta:', response);

            if (!response || !response.status) {
                Swal.fire({
                    title: "Atenção!",
                    text: response ? response.msg : 'Usuário/senha inválidos',
                    icon: "error",
                    timer: 3000
                });
                return;
            }

            await Swal.fire({
                title: "Sucesso!",
                text: response.msg,
                icon: "success",
                timer: 1200,
                showConfirmButton: false
            });

            // Garante que não fique overlay antes do redirecionamento
            limparBackdrop();

            // redireciona
            window.location.href = '/';

        } catch (error) {
            console.error('[Login] erro:', error);
            Swal.fire({
                title: "Erro",
                text: "Ocorreu um erro na autenticação. Verifique o console.",
                icon: "error"
            });
        }
    });
} else {
    console.warn('Botão Login não encontrado (id="Login")');
}

// --- opcional: se houver botão do recuperar dentro do form-recuperar, você pode adicionar handler semelhante ---
// exemplo (ajuste rota quando tiver):
// const recuperarBtns = document.querySelectorAll('#form-recuperar .btn-primary');
// recuperarBtns.forEach(btn => { ... })

// fim do arquivo
