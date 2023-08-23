export interface Validator {
    name: string;
    validator: any;
    message: string;
}
export interface FieldConfig {
    label?: string;
    name?: string;
    inputType?: string;
    options?: string[];
    collections?: any;
    length?: string; 
    type: string;
    loading?: boolean;
    value?: any;
    addButton?: boolean;
    validations?: Validator[];
}