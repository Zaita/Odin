import ThemedButton from "../ThemedButton";

export default function UnlinkModal(props) {
  const { open, itemInfo, onConfirm, onCancel } = props;
  if (!open) {
      return <></>;
  }
  return (
      <div className="fixed inset-0 z-50 overflow-auto bg-smoke-light flex"
        style={{backgroundColor: "rgb(0, 0, 0, 0.5)"}}
      >
          <div className="relative p-4 bg-white w-full max-w-md m-auto flex-col flex rounded-lg h-32">
              <div>
                Are you sure you wish to unlink the {itemInfo.type}: <b>"{itemInfo.name}"</b>?.<br/>
                <br/>
                This won't delete the item from the system, but unlink it immediately.<br/>
                You can re-link the {itemInfo.type} without any data loss.
              </div>
              <span className="absolute bottom-0 right-0 p-2">
              <span className="pr-2"><ThemedButton siteConfig={props.siteConfig} onClick={onConfirm} children="Yes" autofocus/></span>
              <ThemedButton siteConfig={props.siteConfig} onClick={onCancel} children="No/Cancel"/>                  
              </span>
          </div>
      </div>
  );
}