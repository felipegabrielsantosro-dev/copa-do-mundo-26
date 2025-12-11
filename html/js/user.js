import { Validate } from "./Validate.js";
import { Requests } from "./Requests.js";
const Salvar = document.getElementById('salvar');

$('#cpf').inputmask({ "mask": ["999.999.999-99", "99.999.999/9999-99"] });

$('#celular').inputmask({ "mask": ["(99) 99999-9999"] });

Salvar.addEventListener('click', async () => {
    Validate.SetForm('form').Validate();
    const response = await Requests.SetForm('form').Post('/usuario/insert');
    console.log(response);
});
