function scrollTo(linkId, divId)
{

    $("#" + linkId).click(function () {
        $('html, body').animate({
            scrollTop: $("#" + divId).offset().top - getMenuHeight()
        }, 100);
    });

}


function cacherTexte(divId)
{
    $("#" + divId).hide();
}


function AfficherTexte(divId)
{
    $("#" + divId).show();
    $("#divNextText").hide();
}


function InitDisplayServicesBasic()
{

    if (!$("#rdBasic").is(":checked") && !$("#rdAdvanced").is(":checked") && !$("#rdPersonalized").is(":checked"))
    {
        $("#rdBasic").click();
    }
}

function displayBasic()
{
    $("#divPriceBasic").show();
    $("#divPriceAdvanced").hide();
    $("#divPricePersonalized").hide();
}

function displayAdvanced()
{
    $("#divPriceBasic").hide();
    $("#divPriceAdvanced").show();
    $("#divPricePersonalized").hide();
}

function displayPersonalized()
{
    $("#divPriceBasic").hide();
    $("#divPriceAdvanced").hide();
    $("#divPricePersonalized").show();
}

function getMenuHeight()
{
    var divHeight = $("#myNavbar").height();
    var windowsWidth = $(window).width();
    if (windowsWidth <= 750)
        return 40;
    else
        return divHeight;
}

function setBackgroundDefil(divName)
{
    $("#" + divName).backstretch([

        "design/img/Medical/Computer-background.jpg"
                , "design/img//Medical/doctor1.jpg"
                , "design/img//Medical/doctor2.jpg"
                , "design/img//Medical/doctor3.jpg"
                , "design/img/Medical/medical-application.jpg"
                , "design/img/Medical/human-scanner.jpg"
                , "design/img/Medical/surgery.jpg"
    ], {duration: 4000, fade: 1000});

// $("#" + divName).backstretch([
//
//                 "design/img//Medical/slider1.png"
//                , "design/img//Medical/slider2.png"
//                , "design/img//Medical/slider3.png"
//                , "design/img/Medical/slider4.png"
//
//    ], {duration: 2000, fade: 2000});

}



function checkInputRequiredField(Idform)
{

    if (isEmpty("txtName") || isEmpty("txtEmail") || isEmpty("txtSubject") || isEmpty("CommentContact"))
    {

        $("#divContactMessage").addClass("alert alert-danger");
        $("#divContactMessage").text("There are still required fields.");

    } else
    {
        $("#" + Idform).submit();
    }


}

function isEmpty(idElement)
{
    if ($("#" + idElement).val().trim() === "" || $("#" + idElement).val().trim() === null)
        return true;
    else
        return false;
}

function setMessageMailSuccessful(isMail, divId)
{
    if (isMail == 1)
    {
        $('html, body').animate({
            scrollTop: $("#" + divId).offset().top - getMenuHeight()
        }, 100);
        $("#divContactMessage").addClass("alert alert-success");
        $("#divContactMessage").text("Your message has been successfully sent to our Team. Thank you.");

    }
}


function scrollToblock(divId)
{
    $('html, body').animate({
        scrollTop: $("#" + divId).offset().top - getMenuHeight()
    }, 100);
}

function infoProduitCLEO(libelleBouton)
{
    $.alert({
        title: '<h1>Information CLEO</h1>',
        icon: 'fa fa-info-circle fa-2x',
        useBootstrap: false,
        containerFluid: true,
        boxWidth: '50%',
        container: 'body',
        buttons: {
            closeButton: {
                btnClass: 'btn-blue',
                text: libelleBouton,
            }
        },
        content: '<div><p>CLEO (Computer-aided Low-dose Estimation of Osteoporosis disorders) is a software solution that offers a obust analysis of bones from 2D radiographs or 3D scans. This analysis consists of computing several parameters of bone density and microarchitecture (trabecular and cortical) with a high precision.Moreover, our solution allows to reconstruct the 3D bone density and microarchitecture from 2D radiographs. Within CLEO, we provide an efficient bone diagnosis related to osteoporosis diseases.</p></div>',

    });
}


function infoProduitCLES(libelleBouton)
{
    $.alert({
        title: '<h1>Information CLES</h1>',
        icon: 'fa fa-info-circle fa-2x',
        useBootstrap: false,
        containerFluid: true,
        boxWidth: '50%',
        container: 'body',
        buttons: {
            closeButton: {
                btnClass: 'btn-blue',
                text: libelleBouton,
            }
        },
        content: '<div><p>CLES (Computer-aided Low-dose Estimation of Scoliosis disorders) is a software solution for three-dimensional spine reconstruction from 2D radiographs. Moreover, our solution provide an efficient vertebra detection and segmentation from 2D radiographs or 3D reconstructed shapes.</p></div>',

    });
}

function infoProduitMEDBOX(libelleBouton)
{


    $.alert({
        title: '<h1>Information MEDBOX</h1>',
        icon: 'fa fa-info-circle fa-2x',
        useBootstrap: false,
        containerFluid: true,
        boxWidth: '50%',
        container: 'body',
        buttons: {
            closeButton: {
                btnClass: 'btn-blue',
                text: libelleBouton,
            }
        },
        content: '<div><p>MEDBOX (MEDical algorithms Toolbox) provide a set of cloud-based medical imaging algorithms that are mainly used by doctors, rheumatologists and radiologists. The main algorithms are: image registration, image denoising, image filtering, image segmentation, 3D visualization, etc.</p></div>',

    });
}
