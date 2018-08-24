<head>
    <link rel="stylesheet" type='text/css' href='{$css}/planer/default.css' />
    
</head>

<div class="layer" style="padding: 9px; width: 98%;">
<div style="text-align: left; height:30px">
<div class="css3_content_shadow">
<div style="padding: 5px; background-color: #FFFFFF;">

    {$select}
<br>
<br><br>
<h3> Przeglądanie handlowca: {$who} </h3>
<br>
 <table class="Agrohandel__sale__week" style="margin-top:15px;margin-bottom:15px;user-select: text;"> 
 <thead>
        <td class='inter_future' > Data  {$sort_button}</td>
        <td class='inter_future' > Klient </td>
        <td class='inter_future'> Ilość </td>
        <td class='inter_future'> Status  </td>
    </thead>
    {foreach from=$purchases item=purchase}
        <tr>
            <td class='inter_future'>   {$purchase.planed_purchase_date} </td>
            <td class='inter_future' >    {$purchase.company} </td>
            <td class='inter_future' >   {$purchase.amount} </td>
            {if $purchase.status == 'purchased'}
                <td class='inter_future' >  Kupione  </td>
            {else}
                <td class='inter_future' >   Kupione niepotwierdzone </td>
            {/if}
        </tr>
    {/foreach}


 </table>