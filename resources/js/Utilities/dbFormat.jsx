export default function dbFormat(value) {
  value = value.replaceAll(" ", "_").toLowerCase();
  return value;
}