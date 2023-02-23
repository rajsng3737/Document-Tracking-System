
          let deptArr = {
            1:["Naini Agricultural Institute (NAI)","Ethelind College of Home Science (ECHS)","Makino School of Continuing And Non-Formal Education (MSCNE)","College of Forestry (CF)"],
            2:["Jacob Institute of Biotechnology And Bio-Engineering (JIBB) ","Vaugh Institute of Agricultural Engineering And Technology (VIAET)","Warner College of  Dairy Technology (WCDT)"],
            3:["Gospel and Plough Institute of Theology","Department of Advanced Theological Studies","Yeshu Darbar Bible School"],
            4:["Joseph School of Business Studies and Commerce","Chitamber School of Humanities and Social Sciences ","Allahabad School of Education","School of Film and Mass Communication"],
            5:["Shalom Institute of Health and Allied Sciences (SIHAS)"],
            6:["Faculty of Science"]
          };
          function addOptions(x){
            i = 1;
            deptArr[x].forEach(element => {
                  dept.add(new Option(element,i));
                  i++;
                });
          };
          let faculty = document.querySelector("#faculty");
          let dept = document.querySelector("#departments");
          faculty.addEventListener("change",()=>{
            dept.options.length = 1;
            switch(faculty.value){
              case '1':
                addOptions(1);
                break;
              case '2':
                addOptions(2);
                break;
              case '3':
                addOptions(3);
                break;
              case '4':
                addOptions(4);
                break;
              case '5':
                addOptions(5);
                break;
              case '6':
                addOptions(6);
                break;
            }
          });