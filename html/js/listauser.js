import { Requests } from "./Requests.js";

const tabela = new $('#tabela').DataTable({
    paging: true,
    lengthChange: true,
    searching: true,
    ordering: true,
    info: true,
    autoWidth: false,
    responsive: true,
    stateSave: true,
    select: true,
    processing: true,
    serverSide: true,
    language: {
        url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json',
        searchPlaceholder: 'Digite sua pesquisa...'
    },
    ajax: {
        url: '/usuario/listuser',
        type: 'POST'
    }
});

async function deletar(id) {

    document.getElementById('id').value = id;
    const response = await Requests.SetForm('form').Post('/usuario/delete');
    console.log(response);
}
window.deletar = deletar;