function checkBreed() {
    
    var breedInput = document.getElementById('breedInput').value;
    
    var xhr = new XMLHttpRequest();

    
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
           
            document.getElementById('breedResult').innerHTML = xhr.responseText;
        }
    };

    
    xhr.open('GET', 'checkBreed.php?breed=' + breedInput, true);
    
    xhr.send();
}