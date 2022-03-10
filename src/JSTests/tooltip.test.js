/**
 * @jest-environment jsdom
 */
import Tooltip from '../vue-components/tooltip'
import { mount } from '@vue/test-utils';

describe('tooltip',()=>{
    const wrapper = mount(Tooltip);
    it('tooltip',()=>{
        expect(wrapper.exists()).toBe(true);
    });
});