import React, {useState} from "react"
import { DragDropContext, Droppable, Draggable } from 'react-beautiful-dnd';

import ChevronRightIcon from '@mui/icons-material/ChevronRight';

export default function DSRA_ControlList(props) {
  let [renderFlag, setRenderFlag] = useState(false);
  let [displayNA, setDisplayNA] = useState(true);

  function getItems(status) {
    let items = [];
    props.controls.map(element => {
      if (element.implementation_status == status)
        items.push(element);      
    });
    return items;
  }

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
    console.log("Moving: " + removed.name + " (id: " + removed.id + ") to " + id2List[droppableDestination.droppableId]);
    props.callback(removed.id, id2List[droppableDestination.droppableId]);
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
    width: displayNA ? 281 : 376, 
    margin: "2px 2px 2px 2px",
    border: "1px dashed " + props.siteConfig.theme_text_color,
    maxHeight: "600px",
    minHeight: "600px",
    paddingBottom: "10px",
    overflowY: "auto",
  });

  let [currentState, setCurrentState] = useState({
    not_applicable: getItems("not_applicable"),
    not_implemented: getItems("not_implemented"),
    planned: getItems("planned"),
    implemented: getItems("implemented"),
  }); 

  let id2List = {
    droppable: 'not_applicable',
    droppable2: 'not_implemented',
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
        updatedState.not_applicable = source.droppableId === 'droppable' ? items : updatedState.not_applicable;
        updatedState.not_implemented = source.droppableId === 'droppable2' ? items : updatedState.not_implemented;
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
        updatedState.not_applicable = result.droppable ? result.droppable : updatedState.not_applicable;
        updatedState.not_implemented = result.droppable2 ? result.droppable2 : updatedState.not_implemented;
        updatedState.planned = result.droppable3 ? result.droppable3 : updatedState.planned;
        updatedState.implemented = result.droppable4 ? result.droppable4 : updatedState.implemented;

        setCurrentState(updatedState);        
        setRenderFlag(!renderFlag); // needed because updatedState is same array as state
    }
  };

  return (
    <div>
      <div className="mb-4">
        {/* <div className="w-1/4 inline-block">Key Words Input Box</div>
        <div className="w-1/4 inline-block">Risk Category</div>
        <div className="w-1/4 inline-block">Sort By</div> */}
        <div className="w-1/4 inline-block">
          <input type="checkbox" onChange={() => { setDisplayNA(!displayNA) }} defaultChecked={displayNA}/>
          <span className="pl-2">Show Not Applicable</span>
        </div>
      </div>        
      <div style={{ 'display': 'flex' }}>
          <DragDropContext onDragEnd={onDragEnd}>
              {displayNA && <Droppable droppableId="droppable">                
                  {(provided, snapshot) => (
                      <div>
                      <div className="font-bold">Not applicable</div>
                      <div
                          ref={provided.innerRef}
                          style={getListStyle(snapshot.isDraggingOver)}>
                          {currentState.not_applicable.map((item, index) => (
                              <Draggable
                                  key={item.id.toString()}
                                  draggableId={item.id.toString()}
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
                                          <div className="inline-block w-10/12 font-bold">{item.name}</div>
                                          <div className="inline-block w-2/12 font-bold"><ChevronRightIcon/></div>
                                      </div>
                                  )}
                              </Draggable>
                          ))}
                          {provided.placeholder}
                      </div>
                      </div>
                  )}
              </Droppable>}
              <Droppable droppableId="droppable2">
                  {(provided, snapshot) => (
                    <div>
                      <div className="font-bold">Not implemented</div>
                      <div
                          ref={provided.innerRef}
                          style={getListStyle(snapshot.isDraggingOver)}>
                          {currentState.not_implemented.map((item, index) => (
                              <Draggable
                                  key={item.id.toString()}
                                  draggableId={item.id.toString()}
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
                                          <div className="inline-block w-10/12 font-bold">{item.name}</div>
                                          <div className="inline-block w-2/12 font-bold"><ChevronRightIcon/></div>
                                      </div>
                                  )}
                              </Draggable>
                          ))}
                          {provided.placeholder}
                      </div>
                    </div>
                  )}
              </Droppable>
              <Droppable droppableId="droppable3">
                  {(provided, snapshot) => (
                    <div>
                      <div className="font-bold">Planned</div>
                      <div
                          ref={provided.innerRef}
                          style={getListStyle(snapshot.isDraggingOver)}>
                          {currentState.planned.map((item, index) => (
                              <Draggable
                                  key={item.id.toString()}
                                  draggableId={item.id.toString()}
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
                                          <div className="inline-block w-10/12 font-bold">{item.name}</div>
                                          <div className="inline-block w-2/12 font-bold"><ChevronRightIcon/></div>
                                      </div>
                                  )}
                              </Draggable>
                          ))}
                          {provided.placeholder}
                      </div>
                    </div>
                  )}
              </Droppable>  
              <Droppable droppableId="droppable4">
                  {(provided, snapshot) => (
                    <div>
                      <div className="font-bold">Implemented</div>                    
                      <div
                          ref={provided.innerRef}
                          style={getListStyle(snapshot.isDraggingOver)}>
                          {currentState.implemented.map((item, index) => (
                              <Draggable
                                  key={item.id.toString()}
                                  draggableId={item.id.toString()}
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
                                          <div className="inline-block w-10/12 font-bold">{item.name}</div>
                                          <div className="inline-block w-2/12 font-bold"><ChevronRightIcon/></div>
                                      </div>
                                  )}
                              </Draggable>
                          ))}
                          {provided.placeholder}
                      </div>
                    </div>
                  )}
              </Droppable>                      
          </DragDropContext>
      </div>
    </div>
  );
}