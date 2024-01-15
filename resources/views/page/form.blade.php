<x-layouts.panel_layout>

  <x-form_errors :errors="$errors"></x-form_errors>
  <?php //dump($sections)
  ?>
  <?php //dump($page_id)
  ?>


  <div className="body_section">
    <form id="pageForm" action="{{ route('pages.update', $page_id) }}" method="POST">
      @csrf
      {{ method_field('PUT') }}
      <x-page_title>Page editor</x-page_title>
      <script>
        window.csrfToken = @json(csrf_token())
      </script>
      <div class="save-btn-container">
        <input type="hidden" id="index" name="index" value="0" />
        <input class="btn_new_entity" type="submit" value="Salva tutto" />
      </div>
      <script src="https://unpkg.com/react@18/umd/react.development.js" crossorigin></script>
      <script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js" crossorigin></script>
      <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>

      <style>
        .modalClass {
          position: absolute;
          display: 'flex';
        }

        .modaltextClass {
          color: green;
          display: 'flex';
          align-items: "center";
        }

        .modalerrorClass {
          color: red;
          display: 'flex';
          align-items: "center";
        }

        .App {
          display: grid;
          /*border-bottom: 1px solid #000;*/
          border-bottom: 1px solid #e0e0e0;
          padding-bottom: 32px;
          margin-bottom: 24px;
          padding-left: 40px;
          padding-right: 40px;
        }

        .section-title {
          font-weight: 700;
          /*text-decoration: underline;*/
          text-transform: uppercase;
          font-size: 13px;
          line-height: 1;
        }

        h2 {
          font-weight: 700;
          font-size: 13px;
          line-height: 1;
          text-transform: uppercase;
        }

        input,
        select {
          border: 1px solid #e0e0e0 !important;
          border-radius: 5px;
        }

        .save-btn-container {
          text-align: right;
          margin-bottom: 8px;
        }

        .new-section {
          width: 100%;
          text-align: right;
        }

        .section-heading {
          display: grid;
          gap: 8px;
          width: 50%;
          /*grid-template-columns: 2fr 5fr;*/
          grid-template-columns: 1fr;
          margin-top: 24px;
          margin-bottom: 16px;
          align-items: center;
        }

        .section-heading input,
        .section-heading select {
          height: 45px !important;
          /*height: 30px;*/
          border-radius: 5px;
          border: 1px solid #b0b0b0;

          margin-bottom: 16px;
        }

        .slider-container {
          display: grid;
          grid-template-columns: repeat(7, 1fr);
          grid-auto-flow: column;
          gap: 16px;
          width: 100%;
          margin: 0 auto;
          overflow: scroll;
        }

        .slider-item {
          height: 320px;
          cursor: grab;
          display: grid;
          align-items: center;
          background-color: lightgrey;
          position: relative;
          background-size: cover;
          background-position: center;
          min-width: 253px;
        }

        .slider-item h2 {
          text-align: center;
        }

        .slider-item .form {
          display: grid;
          grid-template-columns: 1fr;
          gap: 8px;
          padding: 0 16px;
        }

        .slider-item .form input,
        .slider-item .form select {
          height: 30px;
          border-radius: 5px;
          display: none;
        }

        .slider-item .save-button {
          display: none;
        }

        .buttons-grid {
          position: absolute;
          top: 8px;
          right: 8px;
          display: grid;
          grid-template-columns: repeat(2, 1fr);
          gap: 8px;
        }

        .buttons-grid button svg {
          width: 15px;
          height: 15px;
        }

        .controllers {
          display: grid;
          justify-items: right;
        }

        .controllers button,
        .simple-container button,
        .simple-container-buttons button {
          max-width: 200px;
          background-color: #4791E6;
          color: white;
          padding: 8px 16px;
          height: auto;

          border-radius: 5px;
        }

        .search-results {
          background: white;
          margin-top: -8px;
        }

        .search-results li {
          padding: 8px;
          width: 100%;
          border-bottom: 1px solid #efefef;
          list-style-type: none;
        }

        .simple-container {
          display: grid;
          /*grid-template-columns: 2fr 5fr;*/
          grid-template-columns: 1fr;
          gap: 8px;
          width: 50%;
          margin-top: -8px;
          align-items: center;
        }

        .special-container {
          display: grid;
          /*grid-template-columns: 2fr 5fr;*/
          grid-template-columns: 1fr;
          gap: 8px;
          width: 50%;
          margin-top: 20px;
          align-items: center;
        }

        .simple-container .search-results {
          margin-top: -32px;
          border: 1px solid #e0e0e0;
          border-radius: 5px;
          margin-bottom: 32px;
        }

        .simple-container .search-results li {
          border-top: 1px solid #e0e0e0;
          border-bottom: 1px solid #e0e0e0;
        }

        .simple-container button {
          margin-top: 0;
        }

        .simple-container input,
        .simple-container select {
          margin-bottom: 16px;
          height: 45px !important;
        }

        .simple-container-buttons {
          display: grid;
          grid-template-columns: 2fr 5fr;
          gap: 8px;
          width: 50%;
          margin-top: 8px;
          align-items: center;
        }

        .spinned-input {
          position: relative;
        }

        .spinned-input img {
          position: absolute;
          top: 50%;
          right: 8px;
          width: 28px;
          height: 28px;
          transform: translate(0, -75%);
          display: none;
        }

        .spinned-input-slide {
          position: relative;
        }

        .spinned-input-slide img {
          position: absolute;
          top: 50%;
          right: 8px;
          width: 20px;
          height: 20px;
          transform: translate(0, -50%);
          display: none;
        }

        .rule-grid {
          display: grid;
          width: 100%;
          grid-template-columns: 2fr 2fr 2fr;
          gap: 8px;
        }
      </style>

      <div id="div"></div>

      <script type="text/babel">

        const { useState, useRef, useEffect } = React;

      const ObjPHP = <?php echo json_encode($sections) . ";"; ?>
      const pageId = <?php echo json_encode($page_id) . ";"; ?>
      function CustomSlider(props)
      {
        let state=[[{collection:"",ref:0 ,search_string:" "}]]

        if(props.type=='main'){
          state=props.state[0][0].list?[props.state[0][0].list]:[[{collection:"",ref:0 ,search_string:" "}]]

        }
        const [main,setmain]=useState(state);
        const [showModal,setShowModal]=useState([]);
        const [showError,setShowError]=useState(false);
        //setshowmodal((prev)=>{...prev,id:true})
        document.getElementById('index').value = props?.index;
        const [slides, setSlides] = useState(props.state[0]);
        const [forceEffect, setForceEffect] = useState(0);
        const slidesElement = JSON.stringify(slides);
        const jsonElement = useRef();
        const selectElements = useRef({});
        const textElements = useRef({});
        const spinnerSlide = useRef({});
        const saveButtons = useRef({});
        const idElements = useRef({});
        const backgroundImages = useRef({});
        const searchResults = useRef({});
        
        const sectionLabel = useRef();
        const sectionType = useRef();
        const numberForm = useRef();
        const updateButton = useRef();
        const selectForm = useRef();
        const orderbyForm = useRef();
        const ascDesc = useRef();
        const searchresultsSimple = useRef();
        const IDSimple = useRef();
        const IDRef = useRef();
        const limitForm = useRef();
        const field_1 = useRef([]);
        const operator = useRef([]);
        const fieldValue = useRef([]);
        const spinner = useRef();

        const options = ["main", "custom", "rule"];
        const [myValue, setMyValue] = useState(props?.type);
        

        /*
          * Slides Management
        */
        const addSlide = (e) => {
          e.preventDefault();
          const IDs = [];
          let booleanCheck = false;
          slides.map(slide => {
            IDs.push(slide.ref);
          });
          IDs.sort((a, b) => a - b);
          for(let i=1;i<=IDs.length;i++){
            if(IDs[i-1] !== i){
              setSlides(prevSlides => [...prevSlides, { ref: i }]);
              booleanCheck = true;
              break;
            }
          }
          if(booleanCheck == false){
            setSlides(prevSlides => [...prevSlides, { ref: slides.length + 1 }]);
          }
        };

        const removeSlide = (index, e) => {
          e.preventDefault();
          setSlides((prevSlides) => {
            const updatedSlides = [...prevSlides];
            updatedSlides.splice(index, 1);
            return updatedSlides;
          });
        };

        const editSlide = (index, e) => {
          e.preventDefault();
          setSlides((prevSlides) => {
            const updatedSlides = [...prevSlides];
            textElements.current[index].readOnly = false;
            selectElements.current[index].disabled = false;
            textElements.current[index].style.display = "inline-block";
            selectElements.current[index].style.display = "inline-block";
            saveButtons.current[index].style.display = "inline-block";
            textElements.current[index].addEventListener("input", function(){
              let data = "";
              let xhr = new XMLHttpRequest();
              xhr.withCredentials = true;
              spinnerSlide.current[index].style.display = "inline-block";
              xhr.addEventListener("readystatechange", function() {
                if(this.readyState === 4) {
                  const APIResponse = JSON.parse(this.responseText);
                  const postNode = document.createElement("div");
                  postNode.classList.add("search-results"); 
                  while (searchResults.current[index].firstChild) {
                    searchResults.current[index].removeChild(searchResults.current[index].firstChild);
                  }
                  APIResponse.forEach((element, index) => {
                    const liElement = document.createElement("li");
                    const anchorElement = document.createElement("a");
                    anchorElement.href = "#" + element.id + "#" + element.image_poster.url;
                    anchorElement.id = "id" + index;
                    anchorElement.textContent = element.title;
                    liElement.appendChild(anchorElement);
                    postNode.appendChild(liElement);
                  });
                  searchResults.current[index].style.display = "inline-block";
                  searchResults.current[index].appendChild(postNode);
                  spinnerSlide.current[index].style.display = "none";
                }
              });
              xhr.open("GET", `http://128.140.106.20:9080/${selectElements.current[index].value}/search_by_string?string=${textElements.current[index].value}`);
              xhr.setRequestHeader("Content-Type", "text/plain");
              xhr.send(data);
              searchResults.current[index].addEventListener("click", function(){
                event.preventDefault();
                textElements.current[index].value = event.target.innerText;
                idElements.current[index].value = event.target.href.split("#")[1];
                IDRef.current.value=event.target.href.split('#')[1]?event?.target?.href?.split('#')[1]:null;
                searchResults.current[index].style.display = "none";
                const classNameCustom = "slide-"+(updatedSlides[index].ref);
                const currentSlideDiv = document.getElementsByClassName(classNameCustom);
                currentSlideDiv[0].style.backgroundImage=`url(${event.target.href.split("#")[2]})`;
                currentSlideDiv[0].style.backgroundColor = 'transparent';
                backgroundImages.current[index].value = event.target.href.split("#")[2];
              });
            });
            return updatedSlides;
          });
        };

        const saveSlide = (index, e) => {
          e.preventDefault();
          setSlides((prevSlides) => {
            const updatedSlides = [...prevSlides];
            //updatedSlides[index].type = sectionType.current.value;
            if(typeof IDRef.current !== 'undefined' && IDRef.current != null){
              updatedSlides[index].ref = IDRef.current.value;
            }
            updatedSlides[index].search_string = textElements.current[index].value;
            updatedSlides[index].collection = selectElements.current[index].value;
            updatedSlides[index].background_image = backgroundImages.current[index].value;
            textElements.current[index].readOnly = true;
            selectElements.current[index].disabled = true;
            textElements.current[index].style.display = "none";
            selectElements.current[index].style.display = "none";
            saveButtons.current[index].style.display = "none";
            setForceEffect((prev) => prev + 1);
            return updatedSlides;
          });
        };

        /*
         * Form Management 
        */
        const saveForm = (e,id) => {
          e.preventDefault();
          setSlides((prevSlides) => {
            const updatedSlides = [...prevSlides];
            /* Main Section custom rule */
            if(typeof sectionType.current !== 'undefined' && sectionType.current != null){
              if(updatedSlides[0].type == 'main'){
                numberForm.current.disabled = true;
              }
            }
            /* End of Main Section custom rule */
            if(typeof sectionLabel.current !== 'undefined'){
              updatedSlides[0].label = sectionLabel?.current?.value;
            }
            if(typeof sectionType.current !== 'undefined' && sectionType.current != null){
              updatedSlides[0].type = sectionType?.current?.value;
            }
            if(typeof IDSimple.current !== 'undefined' && IDSimple.current != null){
              updatedSlides[0].ref = IDSimple?.current?.value;
              // updatedSlides[0].ref = 10;
            }
            if(typeof IDRef.current !== 'undefined' && IDRef.current != null){
              updatedSlides[0].ref = IDRef?.current?.value;
            }
            if(typeof selectForm.current !== 'undefined' && selectForm.current != null){
              updatedSlides[0].collection = selectForm?.current?.value;
            }
            if(typeof limitForm.current !== 'undefined' && limitForm.current != null){
              updatedSlides[0].limit = limitForm  ?.current?.value;
            }
            if(typeof numberForm.current !== 'undefined' && numberForm.current != null){
              updatedSlides[0].search_string = numberForm?.current?.value;
            }
            if(typeof orderbyForm.current !== 'undefined' && orderbyForm.current != null){
              updatedSlides[0].order_by = orderbyForm?.current?.value;
            }
            if(typeof ascDesc.current !== 'undefined' && ascDesc.current != null){
              updatedSlides[0].asc_desc = ascDesc?.current?.value;
            }
            if(updatedSlides[0].type=='rule'){
              updatedSlides[0].rules=[{field_1:"",operator:"",field_value:""}]
              updatedSlides[0].rules.forEach((item, index) => {
                item.field_1 = field_1?.current[index]?.value;
                item.operator = operator?.current[index]?.value;
                item.field_value = fieldValue?.current[index]?.value;
              });

            }
            // if(typeof updatedSlides[0].rules == 'object'){
            //   updatedSlides[0].rules.forEach((item, index) => {
            //     item.field_1 = field_1?.current[index]?.value;
            //     item.operator = operator?.current[index]?.value;
            //     item.field_value = fieldValue?.current[index]?.value;
            //   });
            // }
            setmain((prevMain)=>{
            let updatedMain=[...prevMain];
            updatedMain[updatedMain.length-1]=[{collection:updatedSlides[0].collection,
            search_string:updatedSlides[0].search_string,
            ref:updatedSlides[0].ref
            }]
            updatedSlides.map((slide)=>{
            if(slide['type']=='main'){
            slide['list']=[]
            updatedMain.map((m)=>{
              m.map((v)=>{
                slide['list'].push(v);
              })
              
            })
          }
          })
          if(updatedSlides[0].type == 'main'){
                numberForm.current.disabled = false;
                updatedSlides[0].search_string==" "?setShowError(true):setShowError(false);
              }
         
          setShowModal((prev)=>{
            if(!prev?.includes(id)&& updatedSlides[0].search_string!==' ') 
              return [...prev,updatedSlides[0].search_string]
            else return prev
          })
            return updatedMain;

          })
          setForceEffect((prev) => prev + 1);
            return updatedSlides;
          
           
          })
          
        }

        const changeTitle = (e) => {
          e.preventDefault();
          setShowError(false);
          setSlides((prevSlides) => {
            const updatedSlides = [...prevSlides];
            numberForm.current.disabled = false;
            // updateButton.current.disabled = true;
            numberForm.current.addEventListener("input", function(){
              let data = "";
              let xhr = new XMLHttpRequest();
              xhr.withCredentials = true;
              spinner.current.style.display = "inline-block";
              xhr.addEventListener("readystatechange", function()  {
                if(this.readyState === 4) {
                  const APIResponse = JSON.parse(this.responseText);
                  const postNode = document.createElement("div");
                  postNode.classList.add("search-results"); 
                  while (searchresultsSimple.current.firstChild) {
                    searchresultsSimple.current.removeChild(searchresultsSimple.current.firstChild);
                  }
                  APIResponse.forEach((element, index) => {
                    const liElement = document.createElement("li");
                    const anchorElement = document.createElement("a");
                    anchorElement.href = "#" + element.id;
                    anchorElement.id = "id" + index;
                    anchorElement.textContent = element.title;
                    anchorElement.textContent = element.title;
                    liElement.appendChild(anchorElement);
                    postNode.appendChild(liElement);
                  });
                  searchresultsSimple.current.style.display = "inline-block";
                  searchresultsSimple.current.appendChild(postNode);
                  spinner.current.style.display = "none";
                }
              });
              xhr.open("GET", `http://128.140.106.20:9080/${selectForm.current.value}/search_by_string?string=${numberForm.current.value}`);
              xhr.setRequestHeader("Content-Type", "text/plain");
              xhr.send(data);
              searchresultsSimple.current.addEventListener("click", function(){
                event.preventDefault();
                numberForm.current.value = event.target.innerText;
                IDSimple.current.value = event.target.href.split("#")[1];
                searchresultsSimple.current.style.display = "none";
              });
            });
            return updatedSlides;
          })
        }
        

        /*
          * JSON Management
        */
        useEffect(() => { 
          // const mainSection=JSON.parse(slidesElement)
          // const outerArray = mainSection[0];
          // const listArray = outerArray.list;
          // console.log("---------hdfjsdf------->",outerArray);
          if(sectionType.current.value == 'custom'){
            jsonElement.current.value = JSON.stringify({
              type: sectionType?.current?.value,
              label: sectionLabel?.current?.value,
              list: JSON.parse(slidesElement)
            });}
        //     if(sectionType.current.value == 'main'){
        //      jsonElement.current.value = JSON.stringify({
        //       type: sectionType?.current?.value,
        //       label: sectionLabel?.current?.value,
        //       list: listArray
        //     });
        //  }
         else {
            jsonElement.current.value = slidesElement;
          }
          console.log(jsonElement.current.value);
               }, [slidesElement, forceEffect]);

        /*const submitJsons = (index) => {
          console.log(jsonElement);
          console.log("###");
        };*/

        /*
          * Drag&Drop Management
        */
        const handleDragStart = (e, index) => {
          e.dataTransfer.setData("text/plain", index.toString());
        };
        const handleDragOver = (e) => {
          e.preventDefault(jsonElement.current);
        };
        const handleDrop = (e, dropIndex) => {
          const dragIndex = parseInt(e.dataTransfer.getData("text/plain"));
          const updatedSlides = [...slides];
          const [draggedSlide] = updatedSlides.splice(dragIndex, 1);
          updatedSlides.splice(dropIndex, 0, draggedSlide);
          setSlides(updatedSlides);
        };
        const handleDragEnd = (e) => {
          e.preventDefault();
        };
        const addCard=(e,id)=>{
          e.preventDefault();
          setmain((prevMain) => {
          return [
            ...prevMain,
            [
              {
                collection: " ",
                ref: 0,
                search_string: " ",
              },
            ],
          ];
        });
        setShowModal([]);
        } 
        /*
        * HTML Generation
        */
               return (
          <div className="App">
         
          <div className="controllers">
          <button onClick={props.removeSection}>X</button>
        </div>
          <div className="section-heading">
            <h2>Titolo Sezione:</h2>
            <input onChange={(e) => saveForm(e)} className="form_input" type="text" id="section-label" name="section-label" defaultValue={props.label} ref={sectionLabel}/>
            <h2>Tipologia Sezione:</h2>
            <select className="form_select" id="section-type" name="section-type" onChange={(e) => {setMyValue(e?.target?.value); saveForm(e)}} defaultValue={props.type} ref={sectionType}>
            {options.map((option, idx) => (
              <option key={idx}>{option}</option>
            ))}
            </select>
          </div>
          {
            {
              'main': 
              <div>
    {main.map((m, index) => (
      m.map((v, i) => (
       
        <React.Fragment key={`${index}-${i}`}>
        <div className="simple-container">
                    <h2>Collection:</h2>
                    <select className="form_select" name="collection" id="collection" defaultValue={v.collection} ref={selectForm}>
                      <option value="categories">Category</option>
                      <option value="models">Model</option>
                      <option value="videos">Video</option>
                    </select>
                    <h2>Titolo:</h2>
                    <div className="spinned-input">
                      <input className="form_input" type="text" id="search_string" name="search_string" disabled={v.search_string&&v.search_string!==" "?true:false} defaultValue={v.search_string} ref={numberForm}  onChange={(e) => changeTitle(e) }/>
                      <img className="spinner w-20 h-20" src="{{ asset('assets/imgs/spinner.gif') }}" ref={spinner} />
                    </div>
                    <input type="hidden" ref={IDSimple} defaultValue={v.ref} />
                    <div></div>
                    <div ref={searchresultsSimple} />
                    <input type="hidden" id={"jsonResponse"+props.index} name={"jsonResponse"+props.index} value="" ref={jsonElement} />
                  </div>
                  <div className="simple-container-buttons">
                  {v.ref==0?
                    null:<button ref={updateButton} onClick={(e) => changeTitle(e)}>Modifica</button>
                  }
                    <button onClick={(e) => saveForm(e,i)}>Salva</button>
                    <div>
                    {showModal.includes(v.search_string)&&<div className="modaltextClass">
            <div >
            Successfully added to Main.
            </div>
            </div>}
                    </div>
                  </div>
                  <div className="controllers">
                    <button onClick={addCard}>Aggiungi</button>
                  </div>
                  </React.Fragment>
                ))
              ))}
            </div>,
              'rule': 
                <>
                  <div className="simple-container">
                  <h2>Collection:</h2>
                    <select onChange={(e) => saveForm(e)} className="form_select" name="collection" id="collection" defaultValue={props.state[0][0].collection} ref={selectForm}>
                    <option value="categories">Category</option>
                    <option value="models">Model</option>
                    <option value="videos">Video</option>
                  </select>
                  <h2>Limit:</h2>
                    <input onChange={(e) => saveForm(e)} className="form_input" type="number" id="limit" name="limit" defaultValue={props.state[0][0].limit} ref={limitForm} />
                    <h2>Order By:</h2>
                    <select onChange={(e) => saveForm(e)} className="form_select" name="order_by" id="order_by" defaultValue={props.state[0][0].order_by} ref={orderbyForm} >
                      <option value="published_date">Data di Pubblicazione</option>
                    </select>
                    <h2>Asc or Desc:</h2>
                    <select onChange={(e) => saveForm(e)} className="form_select" name="asc_desc" id="asc_desc" defaultValue={props.state[0][0].asc_desc} ref={ascDesc}>
                      <option value="asc">Asc</option>
                      <option value="desc">Desc</option>
                    </select>
                   {(Array.isArray(props.state[0][0].rules)) ?
                        props?.state[0][0].rules.map((item, i) => {  
                        return (
                          <>
                            <h2>Rule:</h2>
                            <div className="rule-grid">
                              <select onChange={(e) => saveForm(e)} className="form_select" name="field_1" id="field_1" defaultValue={item.field_1} ref={(element) => field_1.current[i] = element}>
                                <option value="category_id">Category ID</option>
                                <option value="model_id">Model ID</option>
                                <option value="video_id">Video ID</option>
                              </select>
                              <select onChange={(e) => saveForm(e)} className="form_select" name="operator" id="operator" defaultValue={item.operator} ref={(element) => operator.current[i] = element}>
                                <option value="==">==</option>
                                <option value="!=">!=</option>
                                <option value="in">IN</option>
                                <option value="not-in">NOT IN</option>
                              </select>
                              <input onChange={(e) => saveForm(e)} className="form_input" type="text" id="field_value" name="field_value" defaultValue={item.field_value} ref={(element) => fieldValue.current[i] = element} />
                            </div>
                          </>
                        ) 
                      })
                      :
                      <>
                        <h2>Rule:</h2>
                          <div className="rule-grid">
                            <select onChange={(e) => saveForm(e)} className="form_select" name="field_1" id="field_1" ref={field_1}>
                              <option value="category_id">Category ID</option>
                              <option value="model_id">Model ID</option>
                              <option value="video_id">Video ID</option>
                              <option value="podcast">Podcast</option>
                            </select>
                            <select onChange={(e) => saveForm(e)} className="form_select" name="operator" id="operator" ref={operator}>
                              <option value="==">==</option>
                              <option value="!=">!=</option>
                              <option value="in">IN</option>
                              <option value="not_in">NOT IN</option>
                            </select>
                            <input onChange={(e) => saveForm(e)} className="form_input" type="text" id="field_value" name="field_value" ref={el => (fieldValue.current = [...fieldValue.current, el])} />
                          </div>
                      </>                     
                    }
                    <input type="hidden" id={"jsonResponse"+props.index} name={"jsonResponse"+props.index} value="" ref={jsonElement} />
                  </div>
                </>,
              'custom': 
                <>
                  <div className="slider-container">
                    {slides.map((slide, index) => (
                      <div
                        key={slide.ref}
                        className={`slider-item slide-`+slide.ref}
                        draggable
                        onDragStart={(e) => handleDragStart(e, index)}
                        onDragOver={handleDragOver}
                        onDrop={(e) => handleDrop(e, index)}
                        onDragEnd={handleDragEnd}
                        style={slide.background_image ? { backgroundImage: `url(${slide.background_image})`, backgroundColor: 'transparent' } : slide.image_poster?{ backgroundImage: `url(${slide.image_poster[0].url})`, backgroundColor: 'transparent' }:null}
                      >
                        <div className="form">
                          {/*<h2>ID: {slide.ref}</h2>*/}
                          <select name="select" id="select" disabled={true} ref={(element) => selectElements.current[index] = element}>
                          <option value="categories">Category</option>
                          <option value="models">Model</option>
                          <option value="videos">Video</option>
                          </select>
                          <div className="spinned-input-slide">
                            <input name="search_string" id="search_string" type="text" readOnly={true} ref={(element) => textElements.current[index] = element} defaultValue={slide.search_string} />
                            <img className="spinner w-20 h-20" src="{{ asset('assets/imgs/spinner.gif') }}" ref={(element) => spinnerSlide.current[index] = element} />
                          </div>
                          <input type="hidden" ref={IDRef} defaultValue={slide.id} />
                          <input type="hidden" ref={(element) => idElements.current[index] = element} defaultValue={slide.id} />
                          <input type="hidden" ref={(element) => backgroundImages.current[index] = element} defaultValue={(slide.image_poster)?slide.image_poster[0].url:""} />
                          <div ref={(element) => searchResults.current[index] = element} />
                          <button className="save-button" onClick={(e) => saveSlide(index, e)} ref={(element) => saveButtons.current[index] = element}>Salva</button>
                        </div>
                        <div className="buttons-grid">
                          <div>
                            <button onClick={(e) => editSlide(index, e)}>
                              <svg xmlns="http://www.w3.org/2000/svg" fill="#000000" width="800px" height="800px" viewBox="0 0 32 32" version="1.1">
                                <path d="M30.133 1.552c-1.090-1.044-2.291-1.573-3.574-1.573-2.006 0-3.47 1.296-3.87 1.693-0.564 0.558-19.786 19.788-19.786 19.788-0.126 0.126-0.217 0.284-0.264 0.456-0.433 1.602-2.605 8.71-2.627 8.782-0.112 0.364-0.012 0.761 0.256 1.029 0.193 0.192 0.45 0.295 0.713 0.295 0.104 0 0.208-0.016 0.31-0.049 0.073-0.024 7.41-2.395 8.618-2.756 0.159-0.048 0.305-0.134 0.423-0.251 0.763-0.754 18.691-18.483 19.881-19.712 1.231-1.268 1.843-2.59 1.819-3.925-0.025-1.319-0.664-2.589-1.901-3.776zM22.37 4.87c0.509 0.123 1.711 0.527 2.938 1.765 1.24 1.251 1.575 2.681 1.638 3.007-3.932 3.912-12.983 12.867-16.551 16.396-0.329-0.767-0.862-1.692-1.719-2.555-1.046-1.054-2.111-1.649-2.932-1.984 3.531-3.532 12.753-12.757 16.625-16.628zM4.387 23.186c0.55 0.146 1.691 0.57 2.854 1.742 0.896 0.904 1.319 1.9 1.509 2.508-1.39 0.447-4.434 1.497-6.367 2.121 0.573-1.886 1.541-4.822 2.004-6.371zM28.763 7.824c-0.041 0.042-0.109 0.11-0.19 0.192-0.316-0.814-0.87-1.86-1.831-2.828-0.981-0.989-1.976-1.572-2.773-1.917 0.068-0.067 0.12-0.12 0.141-0.14 0.114-0.113 1.153-1.106 2.447-1.106 0.745 0 1.477 0.34 2.175 1.010 0.828 0.795 1.256 1.579 1.27 2.331 0.014 0.768-0.404 1.595-1.24 2.458z"/>
                              </svg>
                            </button>
                          </div>
                          <div>
                            <button onClick={(e) => removeSlide(index, e)}>X</button>
                          </div>
                        </div>
                      </div>
                    ))}
                  </div>
                  <div className="controllers">
                    <button onClick={addSlide}>Aggiungi Card</button>
                  </div>
                  <input type="hidden" id={"jsonResponse"+props.index} name={"jsonResponse"+props.index} ref={jsonElement} />
                </>
              }[myValue]
            }
          </div>
        );
      }
      
      function App() {
        const [sections, setSections]=useState(ObjPHP);
       
        const handleSectionChange = (newSections) => {
          setSections(newSections);
          setRenderedSections((prevRenderedSections) => {
          const updatedRenderedSections = newSections.map((section, index) => {
            if (section.type === 'custom') {
              return (
                <CustomSlider
                  key={index}
                  state={[section.list]}
                  label={section.label}
                  type={section.type}
                  index={index}
                  removeSection={(e) => removeSection(e, index)}
                />
              );
            } else {
              return (
                <CustomSlider
                  key={index}
                  state={[[section]]}
                  label={section.label}
                  type={section.type}
                  index={index}
                  removeSection={(e) => removeSection(e, index)}
                />
              );
            }
          });
          setComponentsToRender(updatedRenderedSections);
          return updatedRenderedSections;
          });
          };
        const removeSection = async (e, index) => 
        {
        e.preventDefault();
        var pageId = @json($page_id);
        const csrfToken = window.csrfToken;
        const updatedSections = [...sections];
        const newSections = sections.filter((_, i) => i !== index);
        ObjPHP.splice(index,1);
        const removedSection = updatedSections.splice(index, 1)[0];
        const requestData =encodeURIComponent(JSON.stringify({ page: removedSection.label, type:removedSection.type }));
        let xhr = new XMLHttpRequest();
        xhr.open('GET',`http://128.140.106.20:9080/page/destroy?page=${removedSection.label}&type=${removedSection.type}&page_id=${pageId}`);
        xhr.setRequestHeader("Content-Type", "text/plain");
        xhr.setRequestHeader("X-CSRF-TOKEN", window.csrfToken);
        xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                  handleSectionChange(newSections);
                } else {
                    console.error(response.message);
                }
            } else {
                console.error("Error:", xhr.status, xhr.statusText);
                }
              }

                };
            xhr.send();
            };
        const [renderedsections,setRenderedSections] = useState(sections.map((section, index) => {
          if(section.type == 'custom'){
            return(
              <CustomSlider key={index} state={[section.list]} label={section.label} type={section.type} index={index} removeSection={(e) => removeSection(e,index)} />
            );
          } else {
            return(
              <CustomSlider key={index} state={[[section]]} label={section.label} type={section.type} index={index} removeSection={(e) => removeSection(e,index)} />
            );
          }
        }));
        const placeholderSection = useState([{ ref: 1 }]);
        const [componentsToRender, setComponentsToRender] = useState(renderedsections);
        const handleRenderComponent = (e) => {
          e.preventDefault();
          const s=[[{collection:" ",ref:1,search_string:" "}]]

          setComponentsToRender(
          [...componentsToRender, 
          <CustomSlider key={componentsToRender.length} 
            index={componentsToRender.length}
            state={(typeof state !== 'undefined')?state:s} 
            type={(typeof type !== 'undefined')?type:"main"} 
            label={(typeof label !== 'undefined')?label:"New Section"} 
          />
        ]);
        };
        return (
          <div>
            <div className="new-section">
              <button className="btn_new_entity" onClick={handleRenderComponent}>Nuova Sezione</button>
            </div>
            <input className="sections-number" type="hidden" defaultValue={componentsToRender.length} />
            {componentsToRender}
          </div>
        );
      }
      const container = document.getElementById('div');
      const root = ReactDOM.createRoot(container);
      root.render(<App />)
    </script>
      <script>
        document.addEventListener('DOMContentLoaded', function() {
          var categoryForm = document.getElementById('pageForm');

          if (categoryForm) {
            // categoryForm.addEventListener('submit', function (event) {
            //     event.preventDefault(); // Prevent the form from submitting

            //     // Access form elements and their values
            //     var titleValue = categoryForm.querySelector('input[name="title"]').value;
            //     var descriptionValue = categoryForm.querySelector('textarea[name="description"]').value;
            //     var parentIdValue = categoryForm.querySelector('select[name="parent_id"]').value;

            //     // You can now use or display these values as needed
            //     alert('Title: ' + titleValue + '\nDescription: ' + descriptionValue + '\nParent ID: ' + parentIdValue);
            // });
          }
        });
      </script>
    </form>
  </div>
</x-layouts.panel_layout>