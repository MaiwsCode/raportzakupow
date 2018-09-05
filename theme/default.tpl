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
 <table class="ttable ttable-hover ttable-bordered" style="margin-top:15px;margin-bottom:15px;user-select: text;"> 
 <thead>
        <th > Data  {$sort_button}</td>
        <th> Klient </td>
        <th> Ilość </td>
        <th> Status  </td>
    </thead>
     <tbody>
    {foreach from=$purchases item=purchase}
        <tr>
            <td>   {$purchase.planed_purchase_date} </td>
            <td >    {$purchase.company} </td>
            <td  >   {$purchase.amount} </td>
            {if $purchase.status == 'purchased'}
                <td  >  Kupione  </td>
            {else}
                <td  >   Kupione niepotwierdzone </td>
            {/if}
        </tr>
    {/foreach}
    </tbody>


 </table>