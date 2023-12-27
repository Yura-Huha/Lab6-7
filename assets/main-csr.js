const dataTable=document.getElementById('dataTable');
const pageHeader=document.getElementById('pageHeader');
const categoryForm=document.getElementById('categoryForm');
const propertyForm=document.getElementById('propertyForm');
const ebookForm=document.getElementById('ebookForm');
const ebookLink=document.getElementById('ebooLink');
const categoryLink=document.getElementById('categoryLink');
const propertyLink=document.getElementById('propertyLink');
const logoutLink=document.getElementById('logoutLink');
const categorySelect=document.getElementById('ebookCategoryInput');
const contentContainer=document.getElementById('contentContainer');
const loginContainer=document.getElementById('loginContainer');
const loginForm=document.getElementById('loginForm');
const loginErrorText=document.getElementById('loginErrorText');
const propInputsContainer=document.getElementById('propInputsContainer');
const searchForm=document.getElementById('searchForm');
const categoryErrorText = document.getElementById('categoryErrorText');
const categoryDeleteErrorText = document.getElementById('categoryDeleteErrorText');
const propertyErrorText = document.getElementById('propertyErrorText');
const propertyDeleteErrorText = document.getElementById('propertyDeleteErrorText');

function checkLogin(){
    fetch('http://localhost/lab6/app/api/login')
    .then((response) => {
        return response.json();
    })
    .then((data) => {
        console.log(data);
        if(data.userlogin==''){
            loginContainer.style.display='flex';
        }else{
            getCategories();
            getProperties();
            getEbooks();
            contentContainer.style.display='flex';
        }
    });
}

function getCategories(){
    fetch('http://localhost/lab6/app/controllers/categoryController')
    .then((response) => {
        return response.json();
    })
    .then((data) => {
        [].forEach.call(document.querySelectorAll('.app-form'), function (el) {
            el.style.display = 'none';
          });
        categoryForm.style.display='block';
        //console.log(data);
        pageHeader.innerText='Тип';
        let content=``;
        let selectContent=``;
        for (let i=0;i<data.length;i++){
            selectContent+=`<option value="`+data[i].id+`">`+data[i].name+`</option>`
            content+=`<tr>
                        <td>`+data[i].name+`</td>
                        <td>
                            <a class='edit-category' data-id="`+data[i].id+`" href="#">Редагувати</a>
                            <a class='delete-category' data-id="`+data[i].id+`" href="#">Видалити</a>
                        </td>
            </tr>`;
        }
        dataTable.innerHTML=`<thead>
                                <th>Назва</th>
                                <th>Дії</th>
                            </thead>
                            <tbody>
                                `+content+`
                            </tbody>`;
        categorySelect.innerHTML=selectContent;
    });
}

function getProperties(){
    fetch('http://localhost/lab6/app/api/properties')
    .then((response) => {
        return response.json();
    })
    .then((data) => {
        //console.log(data);
        [].forEach.call(document.querySelectorAll('.app-form'), function (el) {
            el.style.display = 'none';
          });
        propertyForm.style.display='block';
        pageHeader.innerText='Характеристики';
        let content=``;
        let inputContent=``
        for (let i=0;i<data.length;i++){
            content+=`<tr>
                        <td>`+data[i].name+`</td>
                        <td>`+data[i].units+`</td>
                        <td>
                            <a class='edit-property' data-id="`+data[i].id+`" href="#">Редагувати</a>
                            <a class='delete-property' data-id="`+data[i].id+`" href="#">Видалити</a>
                        </td>
            </tr>`;
            inputContent+=`<p><input placeholder="`+data[i].name+`" data-id="`+data[i].id+`" class="prop-input" required/></p>`
        }
        dataTable.innerHTML=`<thead>
                                <th>Назва</th>
                                <th>Одиниці вимірювання</th>
                                <th>Дії</th>
                            </thead>
                            <tbody>
                                `+content+`
                            </tbody>`;
        propInputsContainer.innerHTML=inputContent;
    });
}
function getEbooks(){
    fetch('http://localhost/lab6/app/api/ebooks')
    .then((response) => {
        return response.json();
    })
    .then((data) => {
        //console.log(data);
        [].forEach.call(document.querySelectorAll('.app-form'), function (el) {
            el.style.display = 'none';
          });
        ebookForm.style.display='block';
        
        pageHeader.innerText='Електронні книги';
        let content=``;
        for (let i=0;i<data.length;i++){
            let propertyContent=``;
            for (let j=0;j<data[i].properties.length;j++) {
                propertyContent+=data[i].properties[j].name+`: `+data[i].properties[j].value+` `+data[i].properties[j].units+`</br>`;
            }
            content+=`<tr>
                        <td>`+data[i].brand+`</td>
                        <td>`+data[i].model+`</td>
                        <td>`+data[i].category+`</td>
                        <td>`+propertyContent+`</td>
                        <td>
                            <a class='edit-ebook' data-id="`+data[i].id+`" href="#">Редагувати</a>
                            <a class='delete-ebook' data-id="`+data[i].id+`" href="#">Видалити</a>
                        </td>
            </tr>`;
        }
        dataTable.innerHTML=`<thead>
                                <th>Бренд(Виробник)</th>
                                <th>Модель</th>
                                <th>Категорія</th>
                                <th>Характеристики</th>
                                <th>Дії</th>
                            </thead>
                            <tbody>
                                `+content+`
                            </tbody>`;
    });
}
checkLogin();

categoryForm.addEventListener("submit", (event) => {
    event.preventDefault();
    let categoryName=document.getElementById('categoryNameInput').value;
    let categoryId=document.getElementById('categoryIdInput').value;
    if(categoryId==''){
    let formData = new FormData();
    formData.append('name', categoryName);
    fetch("http://localhost/lab6/app/controllers/categoryController",
        {
            body: formData,
            method: "POST"
        }).then((response) => {  return response.json();  }).then((data) => {
            if (data.status == "error") {
                // Вивести помилку
                categoryErrorText.innerText = data.message;
                setTimeout(() => {
                    categoryErrorText.innerText = '';
                }, 3000);
            } else {
                categoryForm.reset();
                getCategories();
            }
        });
} else {
    requestData = { id: categoryId, name: categoryName };

        fetch("http://localhost/lab6/app/controllers/categoryController",
        {
            body: JSON.stringify(requestData),
            method: "PUT"
        }).then((response) => {
            return response.json();
        }).then(()=>{
            categoryForm.reset();
            getCategories();
            document.getElementById('categoryIdInput').value=''
        });
    }
  });
propertyForm.addEventListener("submit", (event) => {
    event.preventDefault();
    let propertyName=document.getElementById('propertyNameInput').value;
    let propertyUnits=document.getElementById('propertyUnitsInput').value;
    let propertyId=document.getElementById('propertyIdInput').value;
    if(propertyId==''){
    let formData = new FormData();
    formData.append('name', propertyName);
    formData.append('units', propertyUnits);
    fetch("http://localhost/lab6/app/api/properties",
        {
            body: formData,
            method: "POST"
        }).then((response) => {
            return response.json();
        }).then((data) => {
            if (data.status == "error") {
                propertyErrorText.innerText = data.message;
                setTimeout(() => {
                    propertyErrorText.innerText = '';
                }, 3000);
            } else {
                propertyForm.reset();
                getProperties();
            }
        });
} else{
        requestData={id:propertyId, name:propertyName,units:propertyUnits};
        fetch("http://localhost/lab6/app/api/properties",
        {
            body: JSON.stringify(requestData),
            method: "PUT"
        }).then((response) => {
            return response.json();
        }).then(()=>{
            propertyForm.reset();
            getProperties();
            document.getElementById('propertyIdInput').value=''
        });
    }
  });
ebookForm.addEventListener("submit", (event) => {
    event.preventDefault();
    let ebookBrand=document.getElementById('ebookBrandInput').value;
    let ebookModel=document.getElementById('ebookModelInput').value;
    let ebookCategory=document.getElementById('ebookCategoryInput').value;
    let ebookId=document.getElementById('ebookIdInput').value;
    if(ebookId==''){
    let formData = new FormData();
    formData.append('brand', ebookBrand);
    formData.append('model', ebookModel);
    formData.append('category', ebookCategory);
    var propInputs = document.getElementsByClassName("prop-input");
    for (var i = 0; i < propInputs.length; i++) {
        formData.append('prop_'+propInputs[i].getAttribute('data-id'), propInputs[i].value);
    }
    fetch("http://localhost/lab6/app/api/ebooks",
        {
            body: formData,
            method: "POST"
        }).then(()=>{
            ebookForm.reset();
            getEbooks();
        });
    }else{
        requestData={id:ebookId, brand:ebookBrand,model:ebookModel, category:ebookCategory};
        var propInputs = document.getElementsByClassName("prop-input");
        for (var i = 0; i < propInputs.length; i++) {
            requestData['prop_'+propInputs[i].getAttribute('data-id')]= propInputs[i].value;
        }
        fetch("http://localhost/lab6/app/api/ebooks",
        {
            body: JSON.stringify(requestData),
            method: "PUT"
        }).then((response) => {
            return response.json();
        }).then(()=>{
            ebookForm.reset();
            getEbooks();
            document.getElementById('ebookIdInput').value=''
        });
    }
  });
loginForm.addEventListener("submit", (event) => {
    event.preventDefault();
    let login=document.getElementById('loginInput').value;
    let password=document.getElementById('passwordInput').value;
    let formData = new FormData();
    formData.append('login', login);
    formData.append('password', password);
    fetch("http://localhost/lab6/app/api/login",
        {
            body: formData,
            method: "POST"
        }).then((response) => {
            return response.json();
        })
        .then((data) => {
            loginForm.reset();
            if(data.error==''){
                loginContainer.style.display='none';
                contentContainer.style.display='flex';
                getCategories();
                getProperties();
                getEbooks();
            } else{
                loginErrorText.innerText=data.error;
            }
        });
  });
  searchForm.addEventListener("submit", (event) => {
    event.preventDefault();
    let searchText=document.getElementById('searchInput').value;
    if(categoryForm.style.display=='block'){
        fetch("http://localhost/lab6/app/controllers/categoryController?search="+searchText,
        {
            method: "GET"
        }).then((response) => {
            return response.json();
        })
        .then((data) => {
            pageHeader.innerText='Категорія';
            let content=``;
        
            for (let i=0;i<data.length;i++){
                content+=`<tr>
                            <td>`+data[i].name+`</td>
                            <td>
                                <a class='edit-category' data-id="`+data[i].id+`" href="#">Редагувати</a>
                                <a class='delete-category' data-id="`+data[i].id+`" href="#">Видалити</a>
                            </td>
                </tr>`;
            }
            dataTable.innerHTML=`<thead>
                                    <th>Назва</th>
                                    <th>Дії</th>
                                </thead>
                                <tbody>
                                    `+content+`
                                </tbody>`;
            });
    } else if(propertyForm.style.display=='block'){
        fetch("http://localhost/lab6/app/api/properties?search="+searchText,
        {
            method: "GET"
        }).then((response) => {
            return response.json();
        })
        .then((data) => {
            pageHeader.innerText='Характеристики';
            let content=``;
        
            for (let i=0;i<data.length;i++){
                content+=`<tr>
                            <td>`+data[i].name+`</td>
                            <td>`+data[i].units+`</td>
                            <td>
                                <a class='edit-property' data-id="`+data[i].id+`" href="#">Редагувати</a>
                                <a class='delete-property' data-id="`+data[i].id+`" href="#">Видалити</a>
                            </td>
                </tr>`;
                }
        dataTable.innerHTML=`<thead>
                                <th>Назва</th>
                                <th>Одиниці вимірювання</th>
                                <th>Дії</th>
                            </thead>
                            <tbody>
                                `+content+`
                            </tbody>`;
                        });

    } else if(ebookForm.style.display=='block'){
        fetch("http://localhost/lab6/app/api/ebooks?search="+searchText,
        {
            method: "GET"
        }).then((response) => {
            return response.json();
        })
        .then((data) => {
            pageHeader.innerText='Електронні книги';
            let content=``;
            for (let i=0;i<data.length;i++){
                let propertyContent=``;
                for (let j=0;j<data[i].properties.length;j++) {
                    propertyContent+=data[i].properties[j].name+`: `+data[i].properties[j].value+` `+data[i].properties[j].units+`</br>`;
                }
                content+=`<tr>
                            <td>`+data[i].brand+`</td>
                            <td>`+data[i].model+`</td>
                            <td>`+data[i].category+`</td>
                            <td>`+propertyContent+`</td>
                            <td>
                                <a class='edit-ebook' data-id="`+data[i].id+`" href="#">Редагувати</a>
                                <a class='delete-ebook' data-id="`+data[i].id+`" href="#">Видалити</a>
                            </td>
                </tr>`;
            }
            dataTable.innerHTML=`<thead>
                                    <th>Бренд(Виробник)</th>
                                    <th>Модель</th>
                                    <th>Категорія</th>
                                    <th>Характеристики</th>
                                    <th>Дії</th>
                                </thead>
                                <tbody>
                                    `+content+`
                                </tbody>`;
                            });
                        }
                        searchForm.reset();
                      });  
  ebookLink.addEventListener("click", (event) => {
    event.preventDefault();
    getEbooks();
  });
categoryLink.addEventListener("click", (event) => {
    event.preventDefault();
    getCategories();
  });
propertyLink.addEventListener("click", (event) => {
    event.preventDefault();
    getProperties();
  });
logoutLink.addEventListener("click", (event) => {
    event.preventDefault();
    fetch('http://localhost/lab6/app/api/login?action=logout')
    .then((response) => {
        return response.json();
    })
    .then((data) => {
        loginContainer.style.display='flex';
        contentContainer.style.display='none';
        loginErrorText.innerText='';
    });
  });
document.body.addEventListener('click', function (e) {
    
    if (e.target.className === 'delete-category') {
        e.preventDefault();          
        fetch("http://localhost/lab6/app/controllers/categoryController?id="+e.target.getAttribute('data-id'),
        {       method: "DELETE"   }).then((response) => {
            return response.json();  }).then((data) => {
                if (data.status == "error") {
                    categoryDeleteErrorText.innerText = data.message;
                    setTimeout(() => {
                        categoryDeleteErrorText.innerText = '';
                    }, 3000);
                } else {
                    getCategories();
                }
            });
    } else
    if (e.target.className === 'delete-property') {
        e.preventDefault();
        fetch("http://localhost/lab6/app/api/properties?id="+e.target.getAttribute('data-id'),
        {                 method: "DELETE"     }).then((response) => {
            return response.json();        }).then((data) => {
                if (data.status == "error") {
                    propertyDeleteErrorText.innerText = data.message;
                    setTimeout(() => {
                        propertyDeleteErrorText.innerText = '';
                    }, 3000);
                } else {
                    getProperties();
                }
            });
    } else
    if (e.target.className === 'delete-ebook') {
        e.preventDefault();
            fetch("http://localhost/lab6/app/api/ebooks?id=" + e.target.getAttribute('data-id'), {
                method: "DELETE"
            }).then((response) => {
                return response.json();
            }).then(() => {
                getEbooks();
            });
    
    } else if(e.target.className === 'edit-category'){
        e.preventDefault();
        fetch('http://localhost/lab6/app/controllers/categoryController?id='+e.target.getAttribute('data-id'))
        .then((response) => {
            return response.json();
        })
        .then((data) => {
            document.getElementById('categoryNameInput').value=data.name;
            document.getElementById('categoryIdInput').value=data.id;
        });
    } else if(e.target.className === 'edit-property'){
        e.preventDefault();
        fetch('http://localhost/lab6/app/api/properties?id='+e.target.getAttribute('data-id'))
        .then((response) => {
            return response.json();
        })
        .then((data) => {
            document.getElementById('propertyUnitsInput').value=data.units;
            document.getElementById('propertyNameInput').value=data.name;
            document.getElementById('propertyIdInput').value=data.id;
        });
    } else if(e.target.className === 'edit-ebook'){
        e.preventDefault();
        fetch('http://localhost/lab6/app/api/ebooks?id='+e.target.getAttribute('data-id'))
        .then((response) => {
            return response.json();
        })
        .then((data) => {
            document.getElementById('ebookBrandInput').value=data.brand;
            document.getElementById('ebookModelInput').value=data.model;
            document.getElementById('ebookCategoryInput').value=data.category_id;
            document.getElementById('ebookIdInput').value=data.id;
            for (i=0;i<data.properties.length;i++){
                document.querySelectorAll(".prop-input[data-id='"+data.properties[i].property_id+"']")[0].value=data.properties[i].value;
            }
            document.querySelectorAll(".prop-input[data-id='2']")[0];
        });
    }
}, false);

