 const cpfInput = document.getElementById('cpf');

  cpfInput.addEventListener('input', function (e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 3 && value.length <= 6)
      value = value.replace(/(\d{3})(\d+)/, '$1.$2');
    else if (value.length > 6 && value.length <= 9)
      value = value.replace(/(\d{3})(\d{3})(\d+)/, '$1.$2.$3');
    else if (value.length > 9)
      value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, '$1.$2.$3-$4');

    e.target.value = value;
  });

  const cardInput = document.getElementById('card-number');

  cardInput.addEventListener('input', function (e) {
    let value = e.target.value.replace(/\D/g, ''); 

    
    value = value.substring(0, 16);

    
    value = value.replace(/(\d{4})(?=\d)/g, '$1 ');

    e.target.value = value;
  });
    const cvvInput = document.getElementById('cvv');

  const expiryInput = document.getElementById('expiry-date');

  expiryInput.addEventListener('input', function (e) {
    let value = e.target.value.replace(/\D/g, ''); 

    if (value.length > 2) {
      value = value.substring(0, 2) + '/' + value.substring(2, 4);
    }


    e.target.value = value.substring(0, 5);
  });
