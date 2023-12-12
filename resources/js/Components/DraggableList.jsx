import { useState, useEffect, createContext, useContext, Fragment } from 'react';
import DragIndicatorIcon from '@mui/icons-material/DragIndicator';

 const initialDnDState = {
  draggedFrom: null,
  draggedTo: null,
  isDragging: false,
  originalOrder: [],
  updatedOrder: []
 }
 
 const DragToReorderList = ({items, callback}) => {
  
  const [list, setList] = useState(items);
  const [dragAndDrop, setDragAndDrop] = useState(initialDnDState);
  
  
  // onDragStart fires when an element
  // starts being dragged
  const onDragStart = (event) => {
   const initialPosition = Number(event.currentTarget.dataset.position);
   
   setDragAndDrop({
    ...dragAndDrop,
    draggedFrom: initialPosition,
    isDragging: true,
    originalOrder: list
   });
   
   
   // Note: this is only for Firefox.
   // Without it, the DnD won't work.
   // But we are not using it.
   event.dataTransfer.setData("text/html", '');
  }
  
  // onDragOver fires when an element being dragged
  // enters a droppable area.
  // In this case, any of the items on the list
  const onDragOver = (event) => {
   
   // in order for the onDrop
   // event to fire, we have
   // to cancel out this one
   event.preventDefault();
   
   let newList = dragAndDrop.originalOrder;
  
   // index of the item being dragged
   const draggedFrom = dragAndDrop.draggedFrom; 
 
   // index of the droppable area being hovered
   const draggedTo = Number(event.currentTarget.dataset.position); 
 
   const itemDragged = newList[draggedFrom];
   const remainingItems = newList.filter((item, index) => index !== draggedFrom);
 
    newList = [
     ...remainingItems.slice(0, draggedTo),
     itemDragged,
     ...remainingItems.slice(draggedTo)
    ];
     
   if (draggedTo !== dragAndDrop.draggedTo){
    setDragAndDrop({
     ...dragAndDrop,
     updatedOrder: newList,
     draggedTo: draggedTo
    })
   }
 
  }
  
  const onDrop = (event) => {
   
   setList(dragAndDrop.updatedOrder);
   
   setDragAndDrop({
    ...dragAndDrop,
    draggedFrom: null,
    draggedTo: null,
    isDragging: false
   });

   callback(event, dragAndDrop.updatedOrder);
  }

  // onDragLeave = () => {
  //   setDragAndDrop({
  //   ...dragAndDrop,
  //   draggedTo: null
  //  });   
  // }
  
  // Not needed, just for logging purposes:
  useEffect( ()=>{
   console.log("Dragged From: ", dragAndDrop && dragAndDrop.draggedFrom);
   console.log("Dropping Into: ", dragAndDrop && dragAndDrop.draggedTo);
  }, [dragAndDrop])
  
  useEffect( ()=>{
   console.log("List updated!");
  }, [list])
  
   return(
     <section>
    <ul id="draggable_list">
     
     {list.map( (item, index) => {
      return(
       <li 
        key={index}
        
        data-position={index}
        draggable
        
        onDragStart={onDragStart}
        onDragOver={onDragOver}
        onDrop={onDrop}
        
        // onDragLeave={onDragLeave}
        
        className={dragAndDrop && dragAndDrop.draggedTo=== Number(index) ? "dropArea" : ""}        
        >
         <span id="icon"><DragIndicatorIcon/></span>
         <span id="item_name">{item.name}</span>   
       </li>
      )
     })}
      
    </ul>
     </section>
     )
 };
 
 export default DragToReorderList;