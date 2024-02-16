import React, {useState} from "react"
import { DragDropContext, Droppable, Draggable } from 'react-beautiful-dnd';

import ChevronRightIcon from '@mui/icons-material/ChevronRight';

export default function DSRA_ControlList(props) {
  let [renderFlag, setRenderFlag] = useState(false);
  let [displayNA, setDisplayNA] = useState(false);

  function getItems(status) {
    let items = [];
    props.controls.forEach(element => {
      if (element.status == status)
        items.push(element);      
    });
    return items;
  }

  // const getItems = (count, offset = 0) =>
  //   Array.from({ length: count }, (v, k) => k).map(k => ({
  //       id: `item-${k + offset}`,
  //       content: `item ${k + offset}`
  // }));

  const reorder = (list, startIndex, endIndex) => {
    const result = Array.from(list);
    const [removed] = result.splice(startIndex, 1);
    result.splice(endIndex, 0, removed);

    return result;
  };
  
  // Move item from one list to other
  const move = (source, destination, droppableSource, droppableDestination) => {
    const sourceClone = Array.from(source);
    const destClone = Array.from(destination);
    const [removed] = sourceClone.splice(droppableSource.index, 1);

    destClone.splice(droppableDestination.index, 0, removed);

    const result = {};
    result[droppableSource.droppableId] = sourceClone;
    result[droppableDestination.droppableId] = destClone;

    return result;
  };

  const grid = 10;

  const getItemStyle = (isDragging, draggableStyle) => ({
      userSelect: 'none',
      padding: grid * 2,
      margin: `0 0 ${grid}px 0`,
      // background: isDragging ? 'lightgreen' : 'grey',
      backgroundColor: props.siteConfig.theme_content_bg_color,
      boxShadow: "2px 2px 3px 0px " + props.siteConfig.theme_header_color,   
      ...draggableStyle
  });

  const getListStyle = isDraggingOver => ({
    background: isDraggingOver ? props.siteConfig.theme_content_bg_color : props.siteConfig.theme_bg_color,
    padding: grid,
    width: displayNA ? 250 : 336, 
    margin: "2px 2px 2px 2px",
    border: "1px dashed " + props.siteConfig.theme_text_color,
    minHeight: "600px",
    paddingBottom: "10px",
  });

  let [currentState, setCurrentState] = useState({
    notApplicable: getItems("not_applicable"),
    notImplemented: getItems("not_implemented"),
    planned: getItems("planned"),
    implemented: getItems("implemented"),
  }); 

  let id2List = {
    droppable: 'notApplicable',
    droppable2: 'notImplemented',
    droppable3: 'planned',
    droppable4: 'implemented',
  };

  function getList(id) { return currentState[id2List[id]] }

  function onDragEnd(result) {
    const { source, destination } = result;

    if (!destination) {
        return;
    }

    // Sorting in same list
    if (source.droppableId === destination.droppableId) {
        const items = reorder(
            getList(source.droppableId),
            source.index,
            destination.index
        );

        let updatedState = currentState;
        updatedState.notApplicable = source.droppableId === 'droppable' ? items : updatedState.notApplicable;
        updatedState.notImplemented = source.droppableId === 'droppable2' ? items : updatedState.notImplemented;
        updatedState.planned = source.droppableId === 'droppable3' ? items : updatedState.planned;
        updatedState.implemented = source.droppableId === 'droppable4' ? items : updatedState.implemented;
        setCurrentState(updatedState);
    }
    // Interlist movement
    else {
        const result = move(
            getList(source.droppableId),
            getList(destination.droppableId),
            source,
            destination
        );

        // Update the state of the two boxes that changed based on the results from move()
        let updatedState = currentState;
        updatedState.notApplicable = result.droppable ? result.droppable : updatedState.notApplicable;
        updatedState.notImplemented = result.droppable2 ? result.droppable2 : updatedState.notImplemented;
        updatedState.planned = result.droppable3 ? result.droppable3 : updatedState.planned;
        updatedState.implemented = result.droppable4 ? result.droppable4 : updatedState.implemented;

        setCurrentState(updatedState);
        setRenderFlag(!renderFlag); // needed because updatedState is same array as state
    }
  };

  return (
    <>
    <div>
      <input type="checkbox" onChange={() => { setDisplayNA(!displayNA) }} defaultChecked={false}/>
    </div>
    <div style={{ 'display': 'flex' }}>
        <DragDropContext onDragEnd={onDragEnd}>
            {displayNA && <Droppable droppableId="droppable">
                {(provided, snapshot) => (
                    <div
                        ref={provided.innerRef}
                        style={getListStyle(snapshot.isDraggingOver)}>
                        {currentState.notApplicable.map((item, index) => (
                            <Draggable
                                key={item.id}
                                draggableId={item.id}
                                index={index}>
                                {(provided, snapshot) => (
                                    <div
                                        ref={provided.innerRef}
                                        {...provided.draggableProps}
                                        {...provided.dragHandleProps}
                                        style={getItemStyle(
                                            snapshot.isDragging,
                                            provided.draggableProps.style
                                        )}>
                                        <div className="inline-block w-10/12 font-bold">{item.content}</div>
                                        <div className="inline-block w-2/12 font-bold"><ChevronRightIcon/></div>
                                    </div>
                                )}
                            </Draggable>
                        ))}
                        {provided.placeholder}
                    </div>
                )}
            </Droppable>}
            <Droppable droppableId="droppable2">
                {(provided, snapshot) => (
                    <div
                        ref={provided.innerRef}
                        style={getListStyle(snapshot.isDraggingOver)}>
                        {currentState.notImplemented.map((item, index) => (
                            <Draggable
                                key={item.id}
                                draggableId={item.id}
                                index={index}>
                                {(provided, snapshot) => (
                                    <div
                                        ref={provided.innerRef}
                                        {...provided.draggableProps}
                                        {...provided.dragHandleProps}
                                        style={getItemStyle(
                                            snapshot.isDragging,
                                            provided.draggableProps.style
                                        )}>
                                        <div className="inline-block w-10/12 font-bold">{item.content}</div>
                                        <div className="inline-block w-2/12 font-bold"><ChevronRightIcon/></div>
                                    </div>
                                )}
                            </Draggable>
                        ))}
                        {provided.placeholder}
                    </div>
                )}
            </Droppable>
            <Droppable droppableId="droppable3">
                {(provided, snapshot) => (
                    <div
                        ref={provided.innerRef}
                        style={getListStyle(snapshot.isDraggingOver)}>
                        {currentState.planned.map((item, index) => (
                            <Draggable
                                key={item.id}
                                draggableId={item.id}
                                index={index}>
                                {(provided, snapshot) => (
                                    <div
                                        ref={provided.innerRef}
                                        {...provided.draggableProps}
                                        {...provided.dragHandleProps}
                                        style={getItemStyle(
                                            snapshot.isDragging,
                                            provided.draggableProps.style
                                        )}>
                                        <div className="inline-block w-10/12 font-bold">{item.content}</div>
                                        <div className="inline-block w-2/12 font-bold"><ChevronRightIcon/></div>
                                    </div>
                                )}
                            </Draggable>
                        ))}
                        {provided.placeholder}
                    </div>
                )}
            </Droppable>  
            <Droppable droppableId="droppable4">
                {(provided, snapshot) => (
                    <div
                        ref={provided.innerRef}
                        style={getListStyle(snapshot.isDraggingOver)}>
                        {currentState.implemented.map((item, index) => (
                            <Draggable
                                key={item.id}
                                draggableId={item.id}
                                index={index}>
                                {(provided, snapshot) => (
                                    <div
                                        ref={provided.innerRef}
                                        {...provided.draggableProps}
                                        {...provided.dragHandleProps}
                                        style={getItemStyle(
                                            snapshot.isDragging,
                                            provided.draggableProps.style
                                        )}>
                                        <div className="inline-block w-10/12 font-bold">{item.content}</div>
                                        <div className="inline-block w-2/12 font-bold"><ChevronRightIcon/></div>
                                    </div>
                                )}
                            </Draggable>
                        ))}
                        {provided.placeholder}
                    </div>
                )}
            </Droppable>                      
        </DragDropContext>
    </div>
    </>
  );
}